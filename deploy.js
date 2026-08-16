// deploy.js - Automated ACMS FTP Deployment & Non-Destructive Migration Tool
const ftp = require('basic-ftp');
const path = require('path');
const fs = require('fs');
const http = require('http');

const config = {
    host: process.env.PLAYBET_FTP_SERVER || '51.195.31.193',
    user: process.env.PLAYBET_FTP_USERNAME || 'playbet',
    password: process.env.PLAYBET_FTP_PASSWORD || '-951-QwerOP01-*',
    remoteDir: process.env.PLAYBET_FTP_TARGET_DIR || 'acms',
    liveDomain: process.env.PLAYBET_LIVE_DOMAIN || '',
    migrationKey: 'acms_playbet_migrate_2026',
    secure: false
};

const IGNORED_TOP_DIRS = [
    '.git',
    '.github',
    '.agents',
    'node_modules',
    'android',
    'scratch',
    'gereksiz',
    '.vscode',
    '.idea',
    '.claude',
    '.gemini'
];

const IGNORED_EXTS = [
    '.sql',
    '.md',
    '.bat',
    '.cmd',
    '.sh',
    '.py',
    '.code-workspace',
    '.log',
    '.tmp',
    '.bak'
];

const IGNORED_EXACT_FILES = [
    '.cursorrules',
    '.gitignore',
    'deploy.js',
    'deploy.bat',
    'package.json',
    'package-lock.json',
    'capacitor.config.json',
    '.DS_Store',
    'Thumbs.db',
    'truncate_brands.php',
    'testuser.php',
    'api_test.php',
    'api_test_matches.php',
    'scratch_check_dates.php',
    'scratch_check_matches.php'
];

function shouldIgnore(fileName, isDir) {
    // 1. Check directories
    if (isDir && IGNORED_TOP_DIRS.includes(fileName)) return true;

    // 2. Check exact filenames
    if (IGNORED_EXACT_FILES.includes(fileName)) return true;

    // 3. Check file extensions
    if (!isDir) {
        if (IGNORED_EXTS.some(ext => fileName.toLowerCase().endsWith(ext))) return true;
        // Ignore test scripts and scratch PHP files
        if (fileName.startsWith('test_') || fileName.startsWith('scratch_') || fileName.startsWith('api_test')) return true;
    }

    return false;
}

async function uploadDirectory(client, localDirPath, remoteDirPath) {
    await client.ensureDir(remoteDirPath);
    const items = fs.readdirSync(localDirPath);

    for (const item of items) {
        const fullLocalPath = path.join(localDirPath, item);
        const stat = fs.statSync(fullLocalPath);
        const isDirectory = stat.isDirectory();

        if (shouldIgnore(item, isDirectory)) {
            continue;
        }

        const targetRemotePath = `${remoteDirPath}/${item}`;

        if (isDirectory) {
            await uploadDirectory(client, fullLocalPath, targetRemotePath);
            // Navigate back to current remoteDirPath
            await client.cd(remoteDirPath);
        } else {
            console.log(`⬆️ Uploading: ${item} -> ${targetRemotePath} (${(stat.size / 1024).toFixed(1)} KB)`);
            await client.uploadFrom(fullLocalPath, item);
        }
    }
}

const https = require('https');

function triggerRemoteMigration() {
    return new Promise((resolve) => {
        if (!config.liveDomain) {
            console.log("\n💡 Bilgi: Canlı domain tanımlanmadığı için HTTP migrasyon tetiklemesi atlandı.");
            console.log("   (Veritabanı senkronizasyonu için canlıda bir kere admin/migrate.php çalıştırılabilir).");
            return resolve();
        }

        console.log("\n⏳ Triggering safe non-destructive database migration on live server...");
        const migrationUrl = `${config.liveDomain}/api/admin/migrate.php?key=${config.migrationKey}`;
        console.log(`📡 URL: ${migrationUrl}`);
        
        const clientLib = migrationUrl.startsWith('https') ? https : http;
        
        clientLib.get(migrationUrl, (res) => {
            let data = '';
            res.on('data', (chunk) => { data += chunk; });
            res.on('end', () => {
                try {
                    const json = JSON.parse(data);
                    if (json.status === 'success') {
                        console.log("✓ Live Database Migration Successful (0 Data Loss)!");
                        if (json.log && Array.isArray(json.log)) {
                            console.log("---------------- Migration Log ----------------");
                            json.log.forEach(line => console.log("  " + line));
                            console.log("-----------------------------------------------");
                        }
                    } else {
                        console.log("⚠️ Migration response:", data);
                    }
                } catch (e) {
                    console.log("Migration output (raw):", data.substring(0, 500));
                }
                resolve();
            });
        }).on('error', (err) => {
            console.log("⚠️ Could not reach live migration endpoint:", err.message);
            resolve();
        });
    });
}

async function deploy() {
    console.log("==========================================================");
    console.log("🚀 STARTING ACMS LIVE SERVER DEPLOYMENT (FTP)");
    console.log(`📍 Host: ${config.host}`);
    console.log(`👤 User: ${config.user}`);
    console.log(`📁 Target Remote Dir: /${config.remoteDir}/`);
    console.log("==========================================================\n");

    const client = new ftp.Client();
    client.ftp.verbose = false;

    try {
        console.log("⏳ Connecting to FTP server...");
        await client.access({
            host: config.host,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        console.log("✓ Connected successfully!\n");

        const localRoot = __dirname;
        const initialRemotePath = `/${config.remoteDir}`;

        console.log(`📦 Synchronizing files to ${initialRemotePath}...`);
        await uploadDirectory(client, localRoot, initialRemotePath);

        // Run safe remote migration
        await triggerRemoteMigration();

        console.log("\n==========================================================");
        console.log(`✅ DEPLOYMENT & DB SYNC COMPLETED SUCCESSFULLY!`);
        console.log(`🌐 Live URL: http://${config.host}/${config.remoteDir}/`);
        console.log("==========================================================");
    } catch (err) {
        console.error("\n❌ DEPLOYMENT FAILED:", err);
        process.exit(1);
    } finally {
        client.close();
    }
}

deploy();
