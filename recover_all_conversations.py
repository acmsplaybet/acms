import sqlite3
import base64
import os
import shutil
import time

def encode_varint(n):
    res = bytearray()
    while n > 0x7f:
        res.append((n & 0x7f) | 0x80)
        n >>= 7
    res.append(n & 0x7f)
    return bytes(res)

def encode_field(tag, wire_type, data):
    key = (tag << 3) | wire_type
    hdr = encode_varint(key)
    if wire_type == 0:
        return hdr + encode_varint(data)
    elif wire_type == 2:
        if isinstance(data, str):
            data = data.encode('utf-8')
        return hdr + encode_varint(len(data)) + data
    elif wire_type == 1:
        return hdr + data
    elif wire_type == 5:
        return hdr + data
    raise ValueError(f"Unknown wire type {wire_type}")

def build_tag9(ws_uri="file:///c%3A/xampp/htdocs/acms"):
    f1 = encode_field(1, 2, ws_uri)
    f2 = encode_field(2, 2, "file:///")
    f3 = encode_field(3, 2, b"")
    return f1 + f2 + f3

def build_timestamp_proto(seconds, nanos=100000000):
    f1 = encode_field(1, 0, seconds)
    f2 = encode_field(2, 0, nanos)
    return f1 + f2

def build_trajectory_entry(title, cid, step_count, ws_uri="file:///c%3A/xampp/htdocs/acms", ts_seconds=None):
    if ts_seconds is None:
        ts_seconds = int(time.time())
        
    ts_proto = build_timestamp_proto(ts_seconds, 100000000)
    
    f1 = encode_field(1, 2, title)
    f2 = encode_field(2, 0, step_count)
    f3 = encode_field(3, 2, ts_proto)
    f4 = encode_field(4, 2, cid)
    f5 = encode_field(5, 0, 1)
    f7 = encode_field(7, 2, ts_proto)
    f9 = encode_field(9, 2, build_tag9(ws_uri))
    f10 = encode_field(10, 2, ts_proto)
    f15 = encode_field(15, 2, b"")
    f16 = encode_field(16, 0, 0)
    
    inner_raw = f1 + f2 + f3 + f4 + f5 + f7 + f9 + f10 + f15 + f16
    inner_b64 = base64.b64encode(inner_raw).decode('ascii')
    
    outer_f1 = encode_field(1, 2, cid)
    outer_sub = encode_field(1, 2, inner_b64)
    outer_f2 = encode_field(2, 2, outer_sub)
    
    return outer_f1 + outer_f2

def do_recovery():
    db_path = r"C:\Users\alper\AppData\Roaming\Antigravity IDE\User\globalStorage\state.vscdb"
    bak_path = r"C:\Users\alper\AppData\Roaming\Antigravity IDE\User\globalStorage\state.vscdb.backup"
    safe_bak = r"C:\Users\alper\AppData\Roaming\Antigravity IDE\User\globalStorage\state.vscdb.full_backup"
    
    if os.path.exists(db_path) and not os.path.exists(safe_bak):
        shutil.copyfile(db_path, safe_bak)
        print(f"Created safe backup: {safe_bak}")

    con = sqlite3.connect(db_path)
    cur = con.cursor()
    cur.execute("SELECT value FROM ItemTable WHERE key = 'antigravityUnifiedStateSync.trajectorySummaries'")
    row = cur.fetchone()
    val = row[0] if row else ""
    dec = base64.b64decode(val) if val else b""

    pos = 0
    existing_entries = {}
    existing_order = []
    while pos < len(dec):
        if dec[pos] != 0x0a:
            break
        pos += 1
        length = 0
        shift = 0
        while True:
            b = dec[pos]
            pos += 1
            length |= (b & 0x7f) << shift
            if not (b & 0x80):
                break
            shift += 7
        entry_bytes = dec[pos:pos+length]
        pos += length
        if entry_bytes.startswith(b'\n$') and len(entry_bytes) >= 38:
            cid = entry_bytes[2:38].decode('ascii')
            existing_entries[cid] = entry_bytes
            existing_order.append(cid)

    ws = "file:///c%3A/xampp/htdocs/acms"
    
    priority_list = [
        {
            "title": "BPA V3 - Sports Analytics & AI Forebet Botu (55 Mesaj)",
            "cid": "6afbc40e-6af4-4549-bed6-b04918927073",
            "steps": 1415,
            "ts": 1786682908
        },
        {
            "title": "ACMS - Canli Onizleme & Timezone & i18n (105 Mesaj - Ana Sohbet)",
            "cid": "365f5bd5-2275-42fe-a870-56c5f28514e5",
            "steps": 2461,
            "ts": 1786712578
        },
        {
            "title": "ACMS - Mockup & Tema Renk Yonetimi (20 Mesaj)",
            "cid": "269c1e6d-cba0-49ce-a4fa-404e520c1d96",
            "steps": 642,
            "ts": 1786658000
        },
        {
            "title": "Sohbet Kurtarma ve Senkronizasyon (Guncel)",
            "cid": "bb347886-c5ba-46b4-bf61-f016256d65f6",
            "steps": 150,
            "ts": int(time.time())
        },
        {
            "title": "Veri Kurtarma ve Senkronizasyon (Onceki Chat)",
            "cid": "15382450-e948-4465-ad38-5b7d34ea7dd8",
            "steps": 259,
            "ts": 1786744311
        },
        {
            "title": "Ilk Kurulum Senkronizasyon",
            "cid": "2b0c6591-41fe-46fd-8b11-0c23cec12ee2",
            "steps": 13,
            "ts": 1786740000
        }
    ]

    new_dict = {}
    final_order = []

    for item in priority_list:
        cid = item["cid"]
        entry_bytes = build_trajectory_entry(
            title=item["title"],
            cid=cid,
            step_count=item["steps"],
            ws_uri=ws,
            ts_seconds=item["ts"]
        )
        new_dict[cid] = entry_bytes
        final_order.append(cid)

    for cid in existing_order:
        if cid not in new_dict and cid in existing_entries:
            new_dict[cid] = existing_entries[cid]
            final_order.append(cid)

    reconstructed_bytes = bytearray()
    for cid in final_order:
        eb = new_dict[cid]
        reconstructed_bytes.append(0x0a)
        reconstructed_bytes.extend(encode_varint(len(eb)))
        reconstructed_bytes.extend(eb)

    reconstructed_b64 = base64.b64encode(reconstructed_bytes).decode('ascii')

    cur.execute("INSERT OR REPLACE INTO ItemTable (key, value) VALUES ('antigravityUnifiedStateSync.trajectorySummaries', ?)", (reconstructed_b64,))
    con.commit()
    con.close()

    if os.path.exists(bak_path):
        con_bak = sqlite3.connect(bak_path)
        cur_bak = con_bak.cursor()
        cur_bak.execute("INSERT OR REPLACE INTO ItemTable (key, value) VALUES ('antigravityUnifiedStateSync.trajectorySummaries', ?)", (reconstructed_b64,))
        con_bak.commit()
        con_bak.close()

    print(f"[{time.strftime('%H:%M:%S')}] Successfully recovered and saved {len(final_order)} conversations into state.vscdb!")

if __name__ == "__main__":
    do_recovery()
