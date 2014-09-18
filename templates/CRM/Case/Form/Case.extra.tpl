{literal}
  <script type="text/javascript">
    cj(document).ready(function() {
      var caseType = cj('#case_type_id option:selected').text();
      if (caseType === "Nieuwehuurdersdossier") {
        cj('#nieuw_woningwaardering').hide();
        cj('#Typeringen').hide();
        cj('#activity_subject').val("Openen dossier nieuwe huurder");
      }
    });     
  </script>
{/literal}
