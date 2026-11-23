[{include file="headitem.tpl" title="PROPULSE_TITLE"|oxmultilangassign box="box"}]

<style>[{$propulseCss}]</style>

<div class="propulse-layout">
    <section class="propulse-card">
        <h1>[{oxmultilang ident="PROPULSE_TITLE"}]</h1>
        <p class="propulse-help">[{oxmultilang ident="PROPULSE_HELP"}]</p>

        <label class="propulse-field" for="propulse-shop-url">
            <span>[{oxmultilang ident="PROPULSE_SHOP_URL"}]</span>
            <span class="propulse-field__row">
                <input type="text" readonly="readonly" id="propulse-shop-url" value="[{$propulseShopUrl|escape}]">
                <button type="button" class="propulse-copy" data-target="propulse-shop-url">[{oxmultilang ident="PROPULSE_COPY"}]</button>
            </span>
        </label>

        <label class="propulse-field" for="propulse-email">
            <span>[{oxmultilang ident="PROPULSE_EMAIL"}]</span>
            <span class="propulse-field__row">
                <input type="text" readonly="readonly" id="propulse-email" value="[{$propulseEmail|escape}]">
                <button type="button" class="propulse-copy" data-target="propulse-email">[{oxmultilang ident="PROPULSE_COPY"}]</button>
            </span>
        </label>

        <label class="propulse-field" for="propulse-password">
            <span>[{oxmultilang ident="PROPULSE_PASSWORD"}]</span>
            <span class="propulse-field__row">
                <input type="text" readonly="readonly" id="propulse-password" value="[{$propulsePassword|escape}]">
                <button type="button" class="propulse-copy" data-target="propulse-password">[{oxmultilang ident="PROPULSE_COPY"}]</button>
            </span>
        </label>

        <p class="propulse-help">[{oxmultilang ident="PROPULSE_QR_HELP"}]</p>
        <div
            id="propulse-qr"
            class="propulse-qr"
            data-email="[{$propulseEmail|escape}]"
            data-password="[{$propulsePassword|escape}]"
            data-shop-url="[{$propulseShopUrl|escape}]"
            data-platform-type="[{$propulsePlatformType|escape}]"
            data-copied="[{oxmultilang ident="PROPULSE_COPIED"}]"
        ></div>
    </section>

    <section class="propulse-card">
        <h2>[{oxmultilang ident="PROPULSE_PERMISSIONS"}]</h2>
        <p><strong>[{oxmultilang ident="PROPULSE_VIEW_ONLY"}]</strong></p>
        <p class="propulse-help">[{oxmultilang ident="PROPULSE_PERMISSIONS_HELP"}]</p>
    </section>
</div>

<script>[{$propulseQrJs}]</script>
<script>[{$propulseAdminJs}]</script>

[{include file="bottomitem.tpl"}]
