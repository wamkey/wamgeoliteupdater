<div class="panel">
  <h3>{l s='Configuration details' d='Modules.Wamgeoliteupdater.Admin'}</h3>

  <strong>{l s='GeoLite2 database metadata' d='Modules.Wamgeoliteupdater.Admin'}</strong>
  <ul>
    <li>{l s='Location: ' d='Modules.Wamgeoliteupdater.Admin'} {$geoIpMeta.dbPath}</li>
    <li>{l s='Build version:' d='Modules.Wamgeoliteupdater.Admin'} {$geoIpMeta.version}</li>
    <li>{l s='Date:' d='Modules.Wamgeoliteupdater.Admin'} {$geoIpMeta.date} (epoch: {$geoIpMeta.epoch})</li>
  </ul>
</div>

<div class="panel">
  <h3>{l s='Settings' d='Modules.Wamgeoliteupdater.Admin'}</h3>

  <a href="{$configUrl}">{l s='Access update options' d='Modules.Wamgeoliteupdater.Admin'}</a>
</div>
