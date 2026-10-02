#!/bin/sh
set -eu

a2dismod -f mpm_event mpm_worker

rm -f /etc/apache2/mods-enabled/mpm_event.load \
      /etc/apache2/mods-enabled/mpm_event.conf \
      /etc/apache2/mods-enabled/mpm_worker.load \
      /etc/apache2/mods-enabled/mpm_worker.conf

a2enmod mpm_prefork
apache2ctl configtest

modules="$(apache2ctl -M)"
printf '%s\n' "$modules"
test "$(printf '%s\n' "$modules" | grep -c 'mpm_.*_module')" -eq 1
printf '%s\n' "$modules" | grep -q 'mpm_prefork_module'

exec apache2-foreground
