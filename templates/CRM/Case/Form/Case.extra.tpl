{literal}
    <script type="text/javascript">
        cj(document).ready(function() {
            var caseType = cj('#case_type_id option:selected').text();
            if (caseType === "Nieuwehuurdersdossier") {
                cj('#customData').hide();
            }
        });     
    </script>
{/literal}
