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
		$("#leftPanel").removeClass("col-sm-offset-3");
		$("#rightPanel").show(500);
	});

	$("#btnClose").click(function() {
		$("#rightPanel").hide();
		$("#leftPanel").addClass("col-sm-offset-3");
	});
});
