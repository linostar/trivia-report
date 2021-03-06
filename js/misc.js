$(document).ready(function() {
	$("#formReport").bootstrapValidator({
		feedbackIcons: {
			valid: "glyphicon glyphicon-ok",
			invalid: "glyphicon glyphicon-remove",
			validating: "glyphicon glyphicon-refresh"
		},
		fields: {
			txtQuestion: {
				validators: {
					notEmpty: {
						message: "Please add the question where the mistake took place"
					}
				}
			},
			txtCaptcha: {
				validators: {
					notEmpty: {
						message: "Please enter the correct solution of the equation"
					}
				}
			}
		}
	});

	$("#idState").val($("#selState").val());

	$("#selState").change(function() {
		$("#idState").val($("#selState").val());
	});

	$("#ckSelectAll").change(function() {
		$(".individualCheckbox").prop("checked", $("#ckSelectAll").is(":checked"));
	});

	$("#btnAddTheme").click(function() {
		$("#actionChange").val("add");
		$("#rightTitle").text("Add Category");
		$("#txtTheme").val("");
		$("#leftPanel").removeClass("col-sm-offset-3");
		$("#rightPanel").show(500);
	});

	$("#btnAddQuestion").click(function() {
		$("#actionQuestionChange").val("add");
		$("#rightTitle").text("Add Question");
		$("#txtQuestion").val("");
		$("#txtAnswer").val("");
		$("#selTheme").val(1);
		$("#leftPanel").removeClass("col-sm-offset-3");
		$("#rightPanel").show(500);
	});

	$("#btnClose").click(function() {
		$("#rightPanel").hide();
		$("#leftPanel").addClass("col-sm-offset-3");
	});

	$(".triggerEdit").click(function() {
		$("#actionChange").val("edit");
		$("#rightTitle").text("Edit Category");
		$("#txtTheme").val($(this).attr("tag_name"));
		$("#txtThemeID").val($(this).attr("tag_id"));
		$("#leftPanel").removeClass("col-sm-offset-3");
		$("#rightPanel").show(500);
	});

	$(".triggerQuestionEdit").click(function() {
		$("#actionQuestionChange").val("edit");
		$("#rightTitle").text("Edit Question");
		$("#txtQuestion").val($(this).attr("tag_question"));
		$("#txtAnswer").val($(this).attr("tag_answer"));
		$("#selTheme").val($(this).attr("tag_theme"));
		$("#txtQuestionID").val($(this).attr("tag_id"));
		$("#leftPanel").removeClass("col-sm-offset-3");
		$("#rightPanel").show(500);
	});

	$("#basic-addon").click(function() {
		$("#formSearch").submit();
	});

	$("#clearSearch").click(function() {
		$("#search").val("");
		$("#search").removeClass("pale");
		$("#formSearch").submit();
	});
});
