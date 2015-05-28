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
                    var i;
                    $('#tracker_issueBundle_issue_assignee').empty();
                    $('#tracker_issueBundle_issue_reporter').empty();

                    if (r.members) {
                        for (i in r.members) {
                            if (r.members.hasOwnProperty(i)) {
                                var member = $('<option>').attr('value', r.members[i].id).html(r.members[i].fullName);
                                $('#tracker_issueBundle_issue_assignee').append(member);
                                $('#tracker_issueBundle_issue_reporter').append(member);
                            }
                        }
                    }
                }
            });
        });
    });

}(jQuery));
