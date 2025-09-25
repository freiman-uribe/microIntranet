<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ShowLastResetLink extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:show-last-link';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show the most recent password reset link from the log file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            $this->error('❌ Log file not found: ' . $logFile);
            $this->newLine();
            $this->comment('💡 The log file will be created when you first use the password reset feature.');
            return Command::FAILURE;
        }

        $this->info('🔍 Searching for the last password reset link...');

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $lastLink = null;
        
        // Search from the end of the file (most recent entries first)
        foreach (array_reverse($lines) as $line) {
            if (strpos($line, 'reset-password') !== false) {
                if (preg_match('/http:\/\/[^\s"]+reset-password\/[a-zA-Z0-9]+[^\s"]*/', $line, $matches)) {
                    $url = rtrim($matches[0], '",');
                    
                    // Extract timestamp from log line
                    if (preg_match('/\[([\d\-\s:]+)\]/', $line, $timeMatches)) {
                        $timestamp = $timeMatches[1];
                    } else {
                        $timestamp = 'Unknown';
                    }
                    
                    // Extract token
                    $token = '';
                    if (preg_match('/reset-password\/([a-zA-Z0-9]+)/', $url, $tokenMatches)) {
                        $token = $tokenMatches[1];
                    }
                    
                    // Extract email
                    $email = '';
                    if (preg_match('/email=([^&\s"]+)/', $url, $emailMatches)) {
                        $email = urldecode($emailMatches[1]);
                    }
                    
                    $lastLink = [
                        'url' => $url,
                        'token' => $token,
                        'email' => $email,
                        'timestamp' => $timestamp
                    ];
                    
                    break; // Only get the most recent one
                }
            }
        }

        if (!$lastLink) {
            $this->error('❌ No password reset links found in the log.');
            $this->newLine();
            $this->comment('💡 To generate a reset link:');
            $this->comment('   1. Go to: ' . config('app.url') . '/forgot-password');
            $this->comment('   2. Enter your email address');
            $this->comment('   3. Run this command again');
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info('🔗 Latest Password Reset Link Found:');
        $this->newLine();
        
        $this->line('📅 <comment>Generated:</comment> ' . $lastLink['timestamp']);
        $this->line('📧 <comment>Email:</comment> ' . ($lastLink['email'] ?: 'Not specified'));
        $this->line('🔑 <comment>Token:</comment> ' . Str::limit($lastLink['token'], 20) . '...');
        $this->newLine();
        
        $this->info('🌐 <comment>Reset URL:</comment>');
        $this->line('<href=' . $lastLink['url'] . '>' . $lastLink['url'] . '</>');
        
        $this->newLine();
        $this->comment('💡 Copy the URL above and paste it in your browser to reset the password.');

        return Command::SUCCESS;
    }
}