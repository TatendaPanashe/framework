<?php

namespace Kodomo;

use Composer\Script\Event;

class Installer
{
    public static function postInstall(Event $event)
    {
        self::checkGlobalInstallation($event);
    }
    
    public static function postUpdate(Event $event)
    {
        self::checkGlobalInstallation($event);
    }
    
    private static function checkGlobalInstallation(Event $event)
    {
        $composer = $event->getComposer();
        $config = $composer->getConfig();
        
        // Check if this is a global installation
        $vendorDir = $config->get('vendor-dir');
        $composerHome = $config->get('home');
        
        if (strpos($vendorDir, $composerHome) !== false) {
            echo "\n🎌 Kodomo Framework installed globally!\n";
            echo "You can now create new projects with: kodomo new project-name\n\n";
        }
    }
    
    public static function createProject($projectName, $targetDir = null)
    {
        $targetDir = $targetDir ?: getcwd() . '/' . $projectName;
        
        if (file_exists($targetDir)) {
            throw new \Exception("Directory '$projectName' already exists!");
        }
        
        // Copy skeleton template
        $skeletonDir = __DIR__ . '/../templates/skeleton';
        
        if (!is_dir($skeletonDir)) {
            throw new \Exception("Skeleton template not found at: $skeletonDir");
        }
        
        self::copyDirectory($skeletonDir, $targetDir);
        
        // Update project composer.json
        self::updateProjectComposer($targetDir, $projectName);
        
        // Make CLI executable
        $cliPath = $targetDir . '/bin/kodomo';
        if (file_exists($cliPath)) {
            chmod($cliPath, 0755);
        }
        
        return $targetDir;
    }
    
    private static function copyDirectory($source, $destination)
    {
        if (!is_dir($destination)) {
            mkdir($destination, 0755, true);
        }
        
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \RecursiveDirectoryIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );
        
        foreach ($iterator as $item) {
            $targetPath = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            
            if ($item->isDir()) {
                if (!is_dir($targetPath)) {
                    mkdir($targetPath, 0755, true);
                }
            } else {
                copy($item, $targetPath);
            }
        }
    }
    
    private static function updateProjectComposer($projectDir, $projectName)
    {
        $composerFile = $projectDir . '/composer.json';
        
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            
            // Update project name
            $composer['name'] = strtolower($projectName) . '/app';
            
            // Add Kodomo Framework dependency
            $composer['require']['kodomo/framework'] = '^1.0';
            
            file_put_contents($composerFile, json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
        }
    }
    
    public static function getVersion()
    {
        $composerFile = __DIR__ . '/../composer.json';
        
        if (file_exists($composerFile)) {
            $composer = json_decode(file_get_contents($composerFile), true);
            return $composer['version'] ?? '1.0.0';
        }
        
        return '1.0.0';
    }
}