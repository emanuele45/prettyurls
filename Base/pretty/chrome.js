//	Pretty URLs Chrome (Admin Interface)

var chrome = chrome || {};

//	Display the live news items
chrome.liveNews = function()
{
	//	Has the live news been loaded yet?
	if (chrome.news)
	{
		var chromeNews = document.getElementById('chrome_news');
		var partial = '';

		for (var i in chrome.news)
			partial += '<h4>' + chrome.news[i].date + '</h4><p>' + chrome.news[i].text + '</p>';

		chromeNews.innerHTML = partial;
	}
	//	Try again in five seconds
	else
		setTimeout(chrome.liveNews, 5000);
}

//	Check the latest version
chrome.currentVersion = '0.9';

chrome.checkVersion = function()
{
	//	Do we have the latest version yet?
	if (chrome.latestVersion)
	{
		var chromeLatest = document.getElementById('chrome_latest');
		var partial = chrome.latestVersion

		//	If we can upgrade, put up a link to do so
		if (chrome.currentVersion != chrome.latestVersion && chrome.currentVersion == chrome.upgradeFrom)
			partial += ' <a href="' + chrome.pmUrl + chrome.upgradeUrl + '">' + chrome.upgradeTxt + '</a>';

		chromeLatest.innerHTML = partial;
	}
	//	Try again in five seconds
	else
		setTimeout(chrome.checkVersion, 5000);
}

//	Events to run once the DOM has fully loaded
chrome.events = {
	DOMLoaded: function()
	{
		//	Make sure this function is run only once
		if (arguments.callee.done) return;
		arguments.callee.done = true;

		//	Run each function
		for (var i in chrome.events.funcs)
			chrome.events.funcs[i]();
	},
	//	An array of functions to run
	funcs: [chrome.liveNews, chrome.checkVersion]
};

if (document.addEventListener)
{
	document.addEventListener('DOMContentLoaded', chrome.events.DOMLoaded, false);
	window.addEventListener('load', chrome.events.DOMLoaded, false);
}
window.onload = chrome.events.DOMLoaded;
