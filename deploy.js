// deploy.js - Automated ACMS FTP Deployment Tool
const ftp = require('basic-ftp');
const path = require('path');
const fs = require('fs');

const config = {
    host: process.env.PLAYBET_FTP_SERVER || '51.195.31.193',
    user: process.env.PLAYBET_FTP_USERNAME || 'playbet',
    password: process.env.PLAYBET_FTP_PASSWORD || '-951-QwerOP01-*',
    remoteDir: process.env.PLAYBET_FTP_TARGET_DIR || 'acms',
    secure: false
};

const IGNORED_PATHS = [
    '.git',
    'node_modules',
    'android',
    'scratch',
    'gereksiz',
    '.vscode',
    '.idea',
    '.claude',
    '.gemini',
    'package-lock.json',
    '.DS_Store',
    'Thumbs.db'
];

function shouldIgnore(relativePath) {
    const parts = relativePath.split(/[\\/]/);
    for (const part of parts) {
        if (IGNORED_PATHS.includes(part)) return true;
        if (part.endsWith('.log') || part.endsWith('.tmp') || part.endsWith('.bak')) return true;
    }
    return false;
}

function getAllFiles(dir, baseDir = dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    for (const file of list) {
        const fullPath = path.join(dir, file);
        const relPath = path.relative(baseDir, fullPath);
        if (shouldIgnore(relPath)) continue;

        const stat = fs.statSync(fullPath);
        if (stat && stat.isDirectory()) {
            results = results.concat(getAllFiles(fullPath, baseDir));
        } else {
            results.push({ fullPath, relPath, size: stat.size });
        }
    }
    return results;
}

async function deploy() {
    console.log("==========================================================");
    console.log("🚀 STARTING ACMS LIVE SERVER DEPLOYMENT (FTP)");
    console.log(`📍 Host: ${config.host}`);
    console.log(`👤 User: ${config.user}`);
    console.log(`📁 Target Remote Dir: ${config.remoteDir}/`);
    console.log("==========================================================\n");

    const client = new ftp.Client();
    client.ftp.verbose = false;

    try {
        const projectRoot = __dirname;
        const filesToUpload = getAllFiles(projectRoot);
        console.log(`📦 Found ${filesToUpload.length} files to synchronize.\n`);

        console.log("⏳ Connecting to FTP server...");
        await client.access({
            host: config.host,
            user: config.user,
            password: config.password,
            secure: config.secure
        });
        console.log("✓ Connected successfully!\n");

        let uploadedCount = 0;
        let totalBytes = 0;

        for (const file of filesToUpload) {
            const remoteFilePath = `${config.remoteDir}/${file.relPath.replace(/\\/g, '/')}`;
            const remoteDirPath = path.posix.dirname(remoteFilePath);

            await client.ensureDir(remoteDirPath);
            await client.uploadFrom(file.fullPath, remoteFilePath);
            
            uploadedCount++;
            totalBytes += file.size;
            process.stdout.write(`\r[${uploadedCount}/${filesToUpload.length}] Uploaded: ${file.relPath} (${(file.size / 1024).toFixed(1)} KB)`);
        }

        console.log("\n\n==========================================================");
        console.log(`✅ DEPLOYMENT COMPLETED SUCCESSFULLY!`);
        console.log(`📊 Summary: ${uploadedCount} files uploaded (${(totalBytes / (1024 * 1024)).toFixed(2)} MB).`);
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
