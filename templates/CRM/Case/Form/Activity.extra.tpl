{literal}
  <script type="text/javascript">
    cj(document).ready(function() {
      var activityTypeName = "{/literal}{$activityTypeName}{literal}";
      if (activityTypeName === 'Plannen bezichtiging' || activityTypeName === 'Afmelden woonkeus' 
            || activityTypeName === 'Tekenen contract' || activityTypeName === 'Versturen aanbiedingsbrief' 
            || activityTypeName === 'Controleren gegevens compleet') {
        cj('#subject').val(activityTypeName);
      }
    });     
  </script>
{/literal}
