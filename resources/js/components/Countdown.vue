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
        let interval = 1000;

        this.interval = setInterval(_ => {
            let currentTime = moment().valueOf();
            let diff = this.eventtime - currentTime;
            let duration = moment.duration(diff, 'milliseconds')
            this.timer = '';
            if (duration.months() > 0) {
                this.timer = duration.months() + 'm · ';
            }
            this.timer += duration.days() + 'd · ' +
                leadZeros(duration.hours(), 2) + ':' +
                leadZeros(duration.minutes(), 2) + ':' +
                leadZeros(duration.seconds(), 2);
            //console.log('Countdown', diff, this.timer, moment(currentTime + diff).format(), duration);
        }, interval)
    }
}
</script>
