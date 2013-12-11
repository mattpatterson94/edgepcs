$(document).foundation();

$(document).ready(function(){
	$("section.our-computers .computers a:first").trigger("click");
});

$("section.our-computers .computers a").click(function(){
	var $computers = $("section.our-computers .computers").find("a");
	var $thiscomputer = $computers.index($(this));

	$("section.our-computers .computers a.active").removeClass("active");
	$(this).addClass("active");

	$.ajax({
		url: "json/test.json"
	}).done(function(data){
		for (var i = 0; i < data.computers.length; i++) {
			if (i == $thiscomputer) {
				$("section.our-computers h4.computer-name").html(data.computers[i].title + "<span class='label'>" + data.computers[i].price + "</span>");

				$("section.our-computers p.computer-desc").html(data.computers[i].desc);

				if (data.computers[i].specs){
					var display = "";
					$.each(data.computers[i].specs, function(e, l){
						$.each(l, function(o, t){
							display += "<div class='row'>";
								display += "<div class='small-2 columns'><p><strong>";
									display += o;
								display += "</strong></p></div>";
								display += "<div class='small-10 columns'><p>";
									display += t;
								display += "</p></div>";
							display += "</div>";
						});
					});
					$("section.our-computers .computer-specs").html(display);
				} else {
					$("section.our-computers .computer-specs").html("<p>No specifications for this computer.</p>");
				}

				return false;
			}
		}
		
	});

	return false;
});

$("section.our-computers a.show-specs").click(function(){
	$(".computer-specs").stop().slideToggle();
	if ($(this).text() == "Show specifications") {
		$(this).text("Hide specifications");
	} else {
		$(this).text("Show specifications");
	}
	return false;
});

$("section.our-computers .addons a").click(function(){
	var item = $(this).parent().next();
	var itemtext = item.text().trim();

	var price = item.next();
	var pricetext = price.text().trim();

	console.log(itemtext + ": " + pricetext);

	return false;
});