<h3>Huurovereenkomsten</h3>

<form method='post' action={$loadHovFile}/>
    <div class='crm-block crm-form-block crm-import-datasource-form-block'>
        <div class='label'>Bronbestand :</div>
        <input type='file' name='source_file_hov' class='form-file' value='Bladeren...'>
    </div>
    <div class='crm-container crm-submit-buttons'>
        <input type='submit' class='form-submit' name='submit_load_hov' value='Laden starten'>   
    </div>
</form>