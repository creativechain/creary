import mqtt from 'mqtt';
import { EventEmitter } from 'events';

class MqttClient extends EventEmitter {
    constructor(url, options = {}) {
        super();
        this.url = url;
        this.options = options;
        this.client = null;
    }

    connect() {
        return new Promise( (resolve, reject) => {
            this.client = mqtt.connect(this.url, this.options);
            this.client.on('connect', function () {
                resolve();
            })
        });
    }

    subscribe() {
        return new Promise( (resolve, reject) => {
            
        })
    }
}
