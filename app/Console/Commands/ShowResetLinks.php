<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Str;

class ShowResetLinks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'reset:show-links {--latest : Show only the latest reset link}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Show password reset links from the log file';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $logFile = storage_path('logs/laravel.log');

        if (!file_exists($logFile)) {
            $this->error('❌ Log file not found: ' . $logFile);
            return Command::FAILURE;
        }

        $this->info('🔍 Searching for password reset links in the log...');
        $this->newLine();

        $content = file_get_contents($logFile);
        $lines = explode("\n", $content);

        $links = [];
        
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
                    
                    $links[] = [
                        'url' => $url,
                        'token' => $token,
                        'email' => $email,
                        'timestamp' => $timestamp
                    ];
                    
                    if ($this->option('latest')) {
                        break; // Only show the latest one
                    }
                }
            }
        }

        if (empty($links)) {
            $this->error('❌ No password reset links found in the log.');
            $this->newLine();
            $this->comment('💡 To generate a reset link:');
            $this->comment('   1. Go to: http://127.0.0.1:8000/forgot-password');
            $this->comment('   2. Enter your email address');
            $this->comment('   3. Run this command again');
            return Command::FAILURE;
        }

        // Show results in a table
        $headers = ['Timestamp', 'Email', 'Token (First 20 chars)', 'Full Reset URL'];
        $rows = [];

        foreach ($links as $link) {
            $rows[] = [
                $link['timestamp'],
                $link['email'] ?: 'Not specified',
                Str::limit($link['token'], 20),
                $link['url']
            ];
        }

        $this->table($headers, $rows);
        
        if (count($links) === 1) {
            $this->newLine();
            $this->info('✅ Copy this URL and paste it in your browser:');
            $this->comment($links[0]['url']);
        } else {
            $this->newLine();
            $this->info('✅ Found ' . count($links) . ' reset link(s). Use --latest to show only the most recent one.');
        }

        return Command::SUCCESS;
    }
}
