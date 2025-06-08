jQuery(document).ready(function($) {
    const schemaRadioButtons = $('.ksas-schema-radio');
    const articleTypeRow = $('#ksas-articletype-row');
    const linkPropsRow = $('#ksas-linkprops-row');
    const anchorRow = $('#ksas-anchor-row');

    function toggleSchemaOptions(selectedMode) {
        if (selectedMode === 'none') {
            articleTypeRow.hide();
            linkPropsRow.hide();
            anchorRow.hide();
        } else {
            articleTypeRow.show();
            if (selectedMode === 'person_ref') {
                linkPropsRow.show();
                anchorRow.show();
            } else {
                linkPropsRow.hide();
                anchorRow.hide();
            }
        }
    }

    if (typeof ksasAdminData !== 'undefined' && ksasAdminData.currentSchemaMode) {
        toggleSchemaOptions(ksasAdminData.currentSchemaMode);
    }

    schemaRadioButtons.on('change', function() {
        const selectedMode = $(this).val();
        toggleSchemaOptions(selectedMode);
    });
});