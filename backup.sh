file=/tmp/alibi-$(/usr/bin/date +\%Y-%m-%d-%H:%M:%S).sql
container=alibi-pgsql-1
/usr/bin/docker container exec $container pg_dump -U sail laravel > $file
mc cp $file felix/alibi
mc mirror /home/projects/alibi/storage/app/public felix/alibi-media
