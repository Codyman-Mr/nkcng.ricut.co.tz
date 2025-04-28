<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\UdpMessage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UdpServer extends Command
{
    protected $signature = 'udp:server';
    protected $description = 'Run a simple UDP server';

    public function handle()
    {
        // Confirm DB connection at startup
        try {
            DB::connection()->getPdo();
            $this->info("Database connection is working.");
        } catch (\Exception $e) {
            $this->error("Could not connect to the database: " . $e->getMessage());
            return;
        }

        $ip = '127.0.0.1';
        $port = 8800;

        try {
            $socket = socket_create(AF_INET, SOCK_DGRAM, SOL_UDP);
            if (!$socket) {
                throw new \Exception("Could not create socket: " . socket_strerror(socket_last_error()));
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
            return;
        }

        if (!socket_bind($socket, $ip, $port)) {
            $this->error("Could not bind socket: " . socket_strerror(socket_last_error()));
            socket_close($socket);
            return;
        }

        $this->info("UDP Server started on {$ip}:{$port}");

        // Optionally log all DB queries
        DB::listen(function ($query) {
            \Log::info("Query executed: " . $query->sql, $query->bindings);
        });

        while (true) {
            $buffer = '';
            $clientIp = '';
            $clientPort = 0;

            $bytesReceived = socket_recvfrom($socket, $buffer, 1024, 0, $clientIp, $clientPort);
            if ($bytesReceived === false) {
                $this->error("Error receiving data: " . socket_strerror(socket_last_error($socket)));
                continue;
            }

            $hexData = bin2hex($buffer);
            $this->info("Received data from $clientIp:$clientPort -> $hexData");
            $this->info("About to insert: IP = $clientIp, Port = $clientPort, Data = $hexData");

            try {
                // Convert binary data to coordinates
                $lat = unpack('E', substr($buffer, 0, 8))[1];
                $lon = unpack('E', substr($buffer, 8, 8))[1];

                $this->info("Received coordinates: lat=$lat, lon=$lon");

                $message = UdpMessage::create([
                    'ip' => $clientIp,
                    'port' => $clientPort,
                    'latitude' => $lat,
                    'longitude' => $lon,
                ]);

                if ($message) {
                    $this->info("Data saved with ID: " . $message->id);
                } else {
                    $this->error("Insert failed without exception.");
                }
            } catch (\Exception $e) {
                $this->error("Database insert error: " . $e->getMessage());
                \Log::error("UDP Server DB Insert Error: " . $e->getMessage());
            }

            // Optionally force a reconnection for long-running process
            DB::purge();
            DB::reconnect();

            $response = "ACK";
            socket_sendto($socket, $response, strlen($response), 0, $clientIp, $clientPort);
        }

        socket_close($socket);
    }
}
