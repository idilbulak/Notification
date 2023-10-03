<?php

class NotificationProcessor {
    protected $redis;
    protected $queueName;

    public function __construct($queueName) {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
        $this->queueName = $queueName;
    }

    public function run() {
        $pid = getmypid();  // Şu anki process id'yi al
        file_put_contents("processorDaemon_$this->queueName.pid", $pid);  // PID dosyasını oluştur

        echo "Starting processor for $this->queueName with PID $pid\n";

        while(true) {
            $this->checkQueue();
            sleep(1);
        }
    }

    protected function checkQueue() {
        $notification = $this->redis->rPop($this->queueName);
        if ($notification) {
            $this->processNotification($notification);
        }
    }

    protected function processNotification($notification) {
        // İşleme kodu vardir heralde
    }
}

$queueName = $argv[1];
$processor = new NotificationProcessor($queueName);
$processor->run();