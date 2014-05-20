<div>
    {if not $error }
        <span style="float:left;">Lisans Kodu: {$lc}</span></br>
        <span style="float:left;">Lisans Adı: {$ln}</span></br>
        <span style="float:left;">Lisans IP: {$ip}</span></br>
        <span style="float:left;">Sonlanma Tarihi: {$expiration}</span></br>
        <span style="float:left;">Durum: {$lstatus}</span></br>
    {else}
        {$lerror}
    {/if}
</div>
