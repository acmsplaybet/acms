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

const IGNORED_TOP_DIRS = [
    '.git',
    'node_modules',
    'android',
    'scratch',
    'gereksiz',
    '.vscode',
    '.idea',
    '.claude',
    '.gemini'
];

const IGNORED_EXTS = ['.log', '.tmp', '.bak'];

function shouldIgnore(fileName, isDir) {
    if (IGNORED_TOP_DIRS.includes(fileName)) return true;
    if (fileName === 'package-lock.json' || fileName === '.DS_Store' || fileName === 'Thumbs.db') return true;
    if (!isDir && IGNORED_EXTS.some(ext => fileName.endsWith(ext))) return true;
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

        console.log("\n==========================================================");
        console.log(`✅ DEPLOYMENT COMPLETED SUCCESSFULLY!`);
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
