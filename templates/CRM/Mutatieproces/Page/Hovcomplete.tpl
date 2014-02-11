<h3>Huurovereenkomsten geladen</h3>
<div class='crm-container crm-form-block'>
     {$endMessage}
</div>
     
{* Table with loading errors, only show if there are any *}

{if $error_flag eq 1}
    <div class='crm-container crm-form-block'>
        <h4>Huurovereenkomsten met fouten</h4>
        <table class='crm-container table-view-layout'>
            <tr class='crm-container tr'>
                <th class='crm-container th'>HOV-nummer</th>
                <th class='crm-container th'>VGE-nummer</th>
                <th class='crm-container th'>Foutboodschap</th>
            </tr>
            {foreach from=$error_hovs item=errorHov}
                <tr class='crm-container tr'>
                    <td class='crm-container td'>{$errorHov.hov_nummer}</td>
                    <td class='crm-container td'>{$errorHov.vge_nummer}</td>
                    <td class='crm-container td'>{$errorHov.error_message}</td>
                </tr>
            {/foreach}    
        </table>
    </div>
{/if}
<div class='crm-container crm-form-block'>
    <a title="CiviCRM home" class="dashboard button" href={$homeCiviUrl} <span><div class="icon inform-icon"></div>Klaar!</span></a>
</div>

