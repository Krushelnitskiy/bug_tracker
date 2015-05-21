jQuery(document).ready(function () {
    $('#tracker_issueBundle_issue_project').change(function(){
        $.ajax({
            url:"/app_dev.php/issue/form",
            method:"POST",
            data:{
                projectId: $(this).val()
            },
            success:function(r) {
                console.log(r);
                $('#tracker_issueBundle_issue_assignee').empty();
                $('#tracker_issueBundle_issue_reporter').empty();

                for (i in r.members) {
                    $('#tracker_issueBundle_issue_assignee').append($('<option>').attr('value',r.members[i].id).html(r.members[i].fullName))
                    $('#tracker_issueBundle_issue_reporter').append($('<option>').attr('value',r.members[i].id).html(r.members[i].fullName))
                }
            }
        });
    });
});