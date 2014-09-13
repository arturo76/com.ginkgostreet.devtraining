<div id="help">
  <p>
    {ts}Zipwise provides a service for getting county names from zipcodes. Please
    provide your Zipwise API key to enable CiviCRM to update contacts' counties
    each time an address is updated.{/ts}
  </p>

  <p>
    {ts}If you don't have a Zipwise API key, you can
    <a href="http://www.zipwise.com/webservices/">get one for free</a> in a
    matter of minutes.{/ts}
  </p>
</div>

{* FIELD EXAMPLE: OPTION 1 (AUTOMATIC LAYOUT) *}

{foreach from=$elementNames item=elementName}
  <div class="crm-section">
    <div class="label">{$form.$elementName.label}</div>
    <div class="content">{$form.$elementName.html}</div>
    <div class="clear"></div>
  </div>
{/foreach}

{* FOOTER *}
<div class="crm-submit-buttons">
{include file="CRM/common/formButtons.tpl" location="bottom"}
</div>
