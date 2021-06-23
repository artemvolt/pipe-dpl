const evtSource = new EventSource("//bc/test/sse", {withCredentials: false});
evtSource.onmessage = function(event) {
	const newElement = document.createElement("li");
	const eventList = document.getElementById("list");

	newElement.textContent = "message: " + event.data;
	eventList.appendChild(newElement);
}