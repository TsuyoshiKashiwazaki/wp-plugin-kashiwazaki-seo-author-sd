jQuery(document).ready(function($) {
    // スキーマ設定の表示/非表示切り替え
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

    // タブ切り替え機能
    $('.nav-tab-wrapper .nav-tab').on('click', function(e) {
        e.preventDefault();

        const targetTab = $(this).data('tab');

        // すべてのタブとコンテンツの active クラスを削除
        $('.nav-tab-wrapper .nav-tab').removeClass('nav-tab-active');
        $('.ksas-tab-content').hide();

        // クリックされたタブとコンテンツを active にする
        $(this).addClass('nav-tab-active');
        $('#ksas-tab-' + targetTab).show();
    });
});