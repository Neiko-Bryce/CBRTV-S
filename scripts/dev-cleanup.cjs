#!/usr/bin/env node
/**
 * Dev Cleanup Script
 * Runs before dev server starts to ensure clean state
 * Works on Windows, Mac, and Linux
 */

const fs = require('fs');
const path = require('path');
const { execSync } = require('child_process');

const ROOT = path.join(__dirname, '..');
const HOT_FILE = path.join(ROOT, 'public', 'hot');
const VITE_CACHE = path.join(ROOT, 'node_modules', '.vite');

console.log('\n🧹 Dev Cleanup Starting...\n');

// 1. Kill any existing Vite/Node dev processes (port 5173, 5174, etc.)
function killExistingProcesses() {
    const isWindows = process.platform === 'win32';
    
    try {
        if (isWindows) {
            // Kill node processes on common Vite ports
            const ports = [5173, 5174, 5175, 5176];
            ports.forEach(port => {
                try {
                    // Find PID using port
                    const result = execSync(`netstat -ano | findstr :${port} | findstr LISTENING`, { encoding: 'utf8', stdio: ['pipe', 'pipe', 'pipe'] });
                    const lines = result.trim().split('\n');
                    lines.forEach(line => {
                        const parts = line.trim().split(/\s+/);
                        const pid = parts[parts.length - 1];
                        if (pid && pid !== '0') {
                            try {
                                execSync(`taskkill /PID ${pid} /F`, { stdio: 'pipe' });
                                console.log(`   Killed process on port ${port} (PID: ${pid})`);
                            } catch (e) {
                                // Process might already be dead
                            }
                        }
                    });
                } catch (e) {
                    // No process on this port
                }
            });
        } else {
            // Unix-like systems
            try {
                execSync('pkill -f "vite" 2>/dev/null || true', { stdio: 'pipe' });
            } catch (e) {
                // No vite process
            }
        }
        console.log('✓ Checked for stale processes');
    } catch (e) {
        console.log('✓ No stale processes found');
    }
}

// 2. Remove public/hot file
function removeHotFile() {
    try {
        if (fs.existsSync(HOT_FILE)) {
            fs.unlinkSync(HOT_FILE);
            console.log('✓ Removed public/hot');
        } else {
            console.log('✓ No public/hot file');
        }
    } catch (e) {
        console.log('⚠ Could not remove public/hot:', e.message);
    }
}

// 3. Clear Vite cache
function clearViteCache() {
    try {
        if (fs.existsSync(VITE_CACHE)) {
            fs.rmSync(VITE_CACHE, { recursive: true, force: true });
            console.log('✓ Cleared Vite cache');
        } else {
            console.log('✓ No Vite cache to clear');
        }
    } catch (e) {
        console.log('⚠ Could not clear Vite cache:', e.message);
    }
}

// Run cleanup
killExistingProcesses();
removeHotFile();
clearViteCache();

console.log('\n✅ Cleanup complete! Starting dev servers...\n');
