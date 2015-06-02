(function($){
    $(document).ready(function () {
        $('#tracker_issueBundle_issue_project').change(function(){
            $.ajax({
                url:"/app_dev.php/issue/form",
                method:"POST",
                data:{
                    projectId: $(this).val()
                },
                success:function(r) {
                    var i,
                        inputAssignee = $('#tracker_issueBundle_issue_assignee'),
                        inputReporter = $('#tracker_issueBundle_issue_reporter');

                    inputAssignee.empty();
                    inputReporter.empty();
                    if (r.members) {
                        for (i in r.members) {
                            if (r.members.hasOwnProperty(i)) {
                                var member = $('<option>').attr('value', r.members[i].id).html(r.members[i].fullName);
                                inputAssignee.append(member);
                                inputReporter.append(member);
                            }
                        }
                    }
                }
            });
        });
    });
}(jQuery));
