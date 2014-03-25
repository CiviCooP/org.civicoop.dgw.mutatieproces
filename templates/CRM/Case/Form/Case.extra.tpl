
{assign var=firstUrl value='http://fhpxyp1.a004.woonsuite.nl:7777/portal/page/portal/NCCW/EFP_WOOND_VGE?p_pro_refno=10897&p_pro_refno_temp=&p_par_refno=&p_aun_refno=&p_reftype=PRO&p_last_reftype=&p_hovnr=&p_request'}
<a href="{$firstUrl}" class="edit button" title="{ts}HOV in First{/ts}" id="first-noa-button">
    <span><div class="icon add-icon"></div>{ts}HOV in First{/ts}</span>
</a>
{literal}
    <script type="text/javascript">
        cj(document).ready(function() {
            var caseType = cj('#case_type_id option:selected').text();
            if (caseType === "Nieuwehuurdersdossier") {
                cj('#nieuw_woningwaardering').hide();
                cj('#Typeringen').hide();
                cj('#activity_subject').val("Openen dossier nieuwe huurder");
            }
        cj("#first-noa-button").appendTo("#CaseView");
        });     
    </script>
{/literal}
