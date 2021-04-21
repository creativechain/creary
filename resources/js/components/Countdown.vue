<template>
    <span>{{ timer }}</span>
</template>

<script>

import {leadZeros} from "../lib/util";

export default {
    props: {
        eventtime: [Number]
    },
    data: function () {
        return {
            timer: null,
            interval: null
        }
    },
    mounted: function () {
        console.log('eventDate', this.eventtime, moment(this.eventtime))
        let currentTime = moment().valueOf();
        let diffTime = this.eventtime - currentTime;
        let duration = moment.duration(diffTime, 'milliseconds');
        let interval = 1000;

        this.interval = setInterval(_ => {
            duration = moment.duration(duration - interval, 'milliseconds')
            this.timer = duration.days() + 'd · ' +
                leadZeros(duration.hours(), 2) + ':' +
                leadZeros(duration.minutes(), 2) + ':' +
                leadZeros(duration.seconds(), 2);
            //console.log('Countdown', this.timer);
        }, interval)
    }
}
</script>
