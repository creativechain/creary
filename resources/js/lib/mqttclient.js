import mqtt from 'mqtt';
import { EventEmitter } from 'events';

class MqttClient extends EventEmitter {
    constructor(url, options = {}) {
        super();
        this.url = url;
        this.options = options;
        this.client = null;
    }

    connect(callback) {
        this.client = mqtt.connect(this.url, this.options);
        this.client.on('connect', callback);
    }

    publish(topic, message, options, callback) {
        if (this.client) {
            this.client.publish(topic, message, options, callback);
        }
    }

    subscribe(topic, options, callback) {
        if (this.client) {
            this.client.subscribe(topic, options, callback);
        }
    }

    unsubscribe(topic, options, callback) {
        if (this.client) {
            this.client.unsubscribe(topic, options, callback)
        }
    }

    end(force, options, cb) {
        if (this.client) {
            this.client.end(force, options, cb);
        }
    }


}
