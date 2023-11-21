FROM debian:stable-slim

RUN apt-get -y update \
    && apt-get install -y cron libfcgi

COPY cmd /etc/cron_cmd

ARG CRON_MINUTES=10

RUN echo "*/${CRON_MINUTES} * * * * su -c" "'$(cat /etc/cron_cmd)'" > /etc/cron.d/scheduler
RUN echo "* * * * * su -c" "'$(cat /etc/cron_cmd2)'" >> /etc/cron.d/scheduler
RUN crontab /etc/cron.d/scheduler

RUN touch /var/log/schedule.log

CMD ["cron", "-f","-l","2"]