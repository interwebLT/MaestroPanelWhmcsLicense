<div>
    {if not $error }
        <span style="float:left;">Lisans Kodu: {$lc}</span></br>
        <span style="float:left;">Lisans Adı: {$ln}</span></br>
        <span style="float:left;">Lisans IP: {$ip}</span></br>
        <span style="float:left;">Sonlanma Tarihi: {$expiration}</span></br>
        <span style="float:left;">Durum: {$lstatus}</span></br><br />
        <span style="float:left;"><input name="" type="button" value="Boşa Çıkart (Reissue)" onClick="window.location.href='clientarea.php?action=productdetails&id={$lserviceid}&modop=custom&a=reissue';window.alert('Lisans boşa çıkartıldı.');" /></span><br /><br />
    {else}
        {$lerror}
    {/if}
</div>
