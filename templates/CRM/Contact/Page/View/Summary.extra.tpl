{*
 +--------------------------------------------------------------------+
 | Customization De Goede Woning - digitaliseren mutatieproces        |
 | Author       :   Erik Hommel (erik.hommel@civicoop.org)            |
 |                  EE-atWork (http://www.ee-atwork.nl)               |
 | Date	        :   16 Jan 2014                                       |
 | Description  :   Add button to jump to opzeggen huurcontract       |
 |                  - if huishouden with active hov                   |
 |                    or organisatie with active hov                  |
 |                    or individual with active relation hoofdhuurder |
 |                    or medehuurder                                  |
 |                                                                    |
 | Copyright (C) 2014 Coöperatieve CiviCooP U.A.                      |
 | Licensed to De Goede Woning under the                              |
 | Academic Free License version 3.0.                                 |
 +--------------------------------------------------------------------+
 *}
{if $show_hov_opzeggen == '1'}
    <li>
        <a href="{crmURL p='civicrm/contact/view/hov_opzeggen' q="reset=1&cid=$hov_opzeggen_contact_id"}" class="edit button" title="{ts}Huurovereenkomst opzeggen{/ts}" id="opzeggenHOV">
            <span><div class="icon add-icon"></div>{ts}Huurovereenkomst opzeggen{/ts}</span>
	</a>
    </li>
{/if}
{literal}
    <script type="text/javascript">
    cj("#opzeggenHOV").appendTo("#actions");
    </script>
{/literal}