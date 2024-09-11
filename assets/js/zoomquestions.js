jQuery(document).ready(function($) {
    // Add new question
    $('#add-question').on('click', function() {
        var index = $('#meeting-questions-wrapper .meeting-question').length;
        var newQuestion = $('#meeting-questions-wrapper .meeting-question').first().clone();

        // Reset all input/select fields
        newQuestion.find('input, select').each(function() {
            var name = $(this).attr('name');
            name = name.replace(/\[\d+\]/, '[' + index + ']');
            $(this).attr('name', name).val('');
        });

        // Hide the choices wrapper and reset its contents
        newQuestion.find('.question-choices-wrapper').hide().find('p').not('.add-choice').remove();

        // Append the new question block
        $('#meeting-questions-wrapper').append(newQuestion);
    });

    // Show or hide the choices wrapper based on the selected question type
    $(document).on('change', '.question-type-selector', function() {
        var $wrapper = $(this).closest('.meeting-question').find('.question-choices-wrapper');
        if ($(this).val() == 'single') {
            $wrapper.show();
        } else {
            $wrapper.hide();
        }
    });

    // Add new choice to a single-choice question
    $(document).on('click', '.add-choice', function() {
        var $wrapper = $(this).closest('.question-choices-wrapper');
        var questionIndex = $(this).closest('.meeting-question').index();
        var choiceIndex = $wrapper.find('p').not('.add-choice').length;

        var newChoice = $('<p><input type="text" placeholder="Enter choice" /></p>');

        newChoice.find('input').each(function() {
            var name = $(this).attr('name') || '';
            name = 'custom_questions[' + questionIndex + '][answers][' + choiceIndex + '][choice]';
            $(this).attr('name', name).val('');
        });

        $wrapper.append(newChoice);
    });
});