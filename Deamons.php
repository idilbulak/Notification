<?php 

abstract class BaseDaemon {
    protected $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    abstract protected function checkQueue();
    
    protected function processNotification($notification) {
        // Genel işleme kodu
    }

    public function run() {
        while(true) {
            $this->checkQueue();
            sleep(1);
        }
    }
}

class HighPriorityDaemon extends BaseDaemon {
    protected function checkQueue() {
        $notification = $this->redis->rPop('high_priority_notifications');
        if ($notification) {
            $this->processNotification($notification);
        }
    }
}

class MediumPriorityDaemon extends BaseDaemon {
    protected function checkQueue() {
        if ($this->redis->lLen('high_priority_notifications') === 0) {
            $notification = $this->redis->rPop('medium_priority_notifications');
            if ($notification) {
                $this->processNotification($notification);
            }
        }
    }
}

class LowPriorityDaemon extends BaseDaemon {
    protected function checkQueue() {
        if ($this->redis->lLen('high_priority_notifications') === 0 && $this->redis->lLen('medium_priority_notifications') === 0) {
            $notification = $this->redis->rPop('low_priority_notifications');
            if ($notification) {
                $this->processNotification($notification);
            }
        }
    }
}

// function checkQueueSize($queue_name, $threshold) {
//     $redis = new Redis();
//     $redis->connect('127.0.0.1', 6379);
    
//     $queue_size = $redis->lLen($queue_name);
    
//     if($queue_size > $threshold) {
//         // Daemon başlatma veya ek işlemci kaynakları tahsis etme logic'i buraya
//     } elseif($queue_size < $threshold) {
//         // Daemonları kapatma veya işlemci kaynaklarını azaltma logic'i buraya
//     }
// }

// // Her bir kuyruğu kontrol edin
// checkQueueSize("high_priority_notifications", 1000);
// checkQueueSize("medium_priority_notifications", 500);
// checkQueueSize("low_priority_notifications", 100);


<?php

class NotificationDaemon {
    protected $redis;

    public function __construct() {
        $this->redis = new Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function run() {
        while(true) {
            if (!$this->checkAndProcessQueue('high_priority')) {
                if (!$this->checkAndProcessQueue('medium_priority')) {
                    $this->checkAndProcessQueue('low_priority');
                }
            }
            sleep(1); // Döngüler arasında 1 saniye bekle
        }
    }

    protected function checkAndProcessQueue(string $queueName): bool {
        $notification = $this->redis->rPop($queueName);
        if ($notification) {
            $this->processNotification($notification);
            return true;
        }
        return false;
    }

    protected function processNotification($notification) {
        // İşleme kodunuzu buraya ekleyin
    }
}

// Daemon'u çalıştır
$daemon = new NotificationDaemon();
$daemon->run();



// brPop farkli deamonlar tarafindan islenmeyi engeller