#! /bin/sh
### BEGIN INIT INFO
# Provides:          shcd
# Required-Start:    $all
# Required-Stop:
# Default-Start:     2 3 4 5
# Default-Stop:
# Short-Description: Starts shc Processes
### END INIT INFO


PATH=/sbin:/usr/sbin:/bin:/usr/bin

. /lib/init/vars.sh
. /lib/lsb/init-functions

SHC_SHEDULER_PIDFILE=/var/run/shcd_sheduler.pid
SHC_SWITCH_SERVER_PIDFILE=/var/run/shcd_switchserver.pid
SHC_SENSOR_TRANSMITTER_PIDFILE=/var/run/shcd_sensor_transmitter.pid

/bin/sleep 20

do_start() {
    if [ -x /etc/rc.local ]; then
            [ "$VERBOSE" != no ] && log_begin_msg "Running local boot scripts (/etc/rc.local)"
        /etc/rc.local
        ES=$?
        [ "$VERBOSE" != no ] && log_end_msg $ES
        return $ES
    fi
}

case "$1" in
    start)
        /usr/bin/php /var/www/shc/index.php app=shc -sh >> /var/log/messages 2>&1  &
        echo $! > $SHC_SHEDULER_PIDFILE
        /usr/bin/php /var/www/shc/index.php app=shc -ss >> /var/log/messages 2>&1  &
        echo $! > $SHC_SWITCH_SERVER_PIDFILE
        /usr/bin/php /var/www/shc/index.php app=shc -st >> /var/log/messages 2>&1  &
        echo $! > $SHC_SENSOR_TRANSMITTER_PIDFILE
        ;;
    restart|reload|force-reload)
        echo "Error: argument '$1' not supported" >&2
        exit 3
        ;;
    stop)
        if [ -f $SHC_SHEDULER_PIDFILE ]; then
           PID=`cat $SHC_SHEDULER_PIDFILE`
        fi
        if [ -f $SHC_SWITCH_SERVER_PIDFILE ]; then
           PID="$PID `cat $SHC_SWITCH_SERVER_PIDFILE`"
        fi
        if [ -f $SHC_SENSOR_TRANSMITTER_PIDFILE ]; then
           PID="$PID `cat $SHC_SENSOR_TRANSMITTER_PIDFILE`"
        fi
        kill -9 $PID && rm -f $SHC_SHEDULER_PIDFILE $SHC_SWITCH_SERVER_PIDFILE $SHC_SENSOR_TRANSMITTER_PIDFILE
        ;;
    *)
        echo "Usage: $0 start|stop" >&2
        exit 3
        ;;
esac
