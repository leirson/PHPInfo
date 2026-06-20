<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['user_role'] !== 'ADMIN') {
    http_response_code(403);
    echo json_encode(['error' => 'Acesso negado. Apenas administradores podem atualizar o sistema.']);
    exit;
}

header('Content-Type: application/json');

// NOTE: Replace these with your actual GitHub username and repository name
$githubUser = 'leirson';
$githubRepo = 'PHPInfo';
$githubBranch = 'main'; // branch to track

$apiUrl = "https://api.github.com/repos/{$githubUser}/{$githubRepo}/commits/{$githubBranch}";
$zipUrl = "https://github.com/{$githubUser}/{$githubRepo}/archive/refs/heads/{$githubBranch}.zip";

// Define a file to store current local version commit hash
$versionFile = __DIR__ . '/.version';
$currentVersion = file_exists($versionFile) ? trim(file_get_contents($versionFile)) : 'unknown';

// Fetch options to satisfy GitHub API requirements (User-Agent is required)
$opts = [
    'http' => [
        'method' => 'GET',
        'header' => [
            'User-Agent: PHP-Update-Script'
        ]
    ]
];
$context = stream_context_create($opts);

$action = $_GET['action'] ?? '';

if ($action === 'check') {
    try {
        $response = @file_get_contents($apiUrl, false, $context);
        if ($response === false) {
            throw new Exception("Falha ao comunicar com o GitHub.");
        }
        
        $data = json_decode($response, true);
        $latestHash = $data['sha'] ?? null;
        
        if (!$latestHash) {
             throw new Exception("Resposta inválida do GitHub.");
        }
        
        // If current version is different from the latest commit hash on the branch, update is available
        $isAvailable = ($latestHash !== $currentVersion);
        
        echo json_encode([
            'update_available' => $isAvailable,
            'current_version' => $currentVersion,
            'latest_version' => $latestHash
        ]);
    } catch(Exception $e) {
        echo json_encode(['error' => $e->getMessage()]);
    }
    exit;
}

if ($action === 'apply') {
    try {
        // Fetch the latest commit hash again
        $response = @file_get_contents($apiUrl, false, $context);
        if ($response === false) {
            throw new Exception("Falha ao comunicar com o GitHub para iniciar a atualização.");
        }
        $data = json_decode($response, true);
        $latestHash = $data['sha'] ?? null;

        if (!$latestHash) {
             throw new Exception("Não foi possível verificar a versão mais recente para baixar.");
        }

        // Download the ZIP file
        $zipFile = __DIR__ . '/update.zip';
        $zipData = @file_get_contents($zipUrl, false, $context);
        if ($zipData === false) {
             throw new Exception("Falha ao baixar o arquivo de atualização.");
        }
        
        file_put_contents($zipFile, $zipData);
        
        // Unzip and apply
        $zip = new ZipArchive;
        if ($zip->open($zipFile) === TRUE) {
            // Depending on github zip format, the root folder is usually `<repo>-<branch>/`
            // We want to extract contents of that folder to the current directory directly
            $extractPath = __DIR__ . '/update_temp';
            if (!is_dir($extractPath)) {
                mkdir($extractPath);
            }
            $zip->extractTo($extractPath);
            $zip->close();
            
            $folderName = scandir($extractPath)[2]; // Get the extracted folder name (ignoring . and ..)
            $sourceDir = $extractPath . '/' . $folderName;
            
            // Move files to current dir
            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($sourceDir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );
            
            foreach ($iterator as $item) {
                $subPathName = $iterator->getSubPathName();
                $targetPath = __DIR__ . '/' . $subPathName;
                
                if ($item->isDir()) {
                    if (!is_dir($targetPath)) {
                        mkdir($targetPath, 0755, true);
                    }
                } else {
                    // Don't overwrite config.php if it exists
                    if ($subPathName === 'config.php' && file_exists($targetPath)) {
                        continue;
                    }
                    copy($item, $targetPath);
                }
            }
            
            // Clean up
            array_map('unlink', glob($sourceDir . '/*.*'));
            // Remove temp dirs
            function rrmdir($dir) {
                if (is_dir($dir)) {
                    $objects = scandir($dir);
                    foreach ($objects as $object) {
                        if ($object != "." && $object != "..") {
                            if (filetype($dir."/".$object) == "dir") rrmdir($dir."/".$object); else unlink($dir."/".$object);
                        }
                    }
                    reset($objects);
                    rmdir($dir);
                }
            }
            rrmdir($extractPath);
            unlink($zipFile);
            
            // Update version file
            file_put_contents($versionFile, $latestHash);
            
            echo json_encode(['success' => true]);
        } else {
            throw new Exception("Falha ao extrair o arquivo de atualização.");
        }
    } catch(Exception $e) {
        if (file_exists($zipFile)) unlink($zipFile);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }
    exit;
}
