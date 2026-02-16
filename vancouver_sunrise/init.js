(function () {
    let lastDate = new Date().toDateString();

    const updateDaylightInfo = () => {
        const currentDate = new Date().toDateString();

        if (currentDate !== lastDate) {
            console.log("Vancouver Sunrise: Date changed, updating toolbar...");

            xhrPost(
                "backend.php",
                {
                    op: "pluginhandler",
                    plugin: "vancouver_sunrise",
                    method: "update",
		},
                (transport) => {
                    const container = document.querySelector(".vancouver-sunrise-container");
                    if (container && transport.responseText) {
                        const temp = document.createElement("div");
                        temp.innerHTML = transport.responseText;
                        const newContainer = temp.firstChild;
                        if (newContainer) {
                            container.parentNode.replaceChild(newContainer, container);
                            lastDate = currentDate;
                        }
                    }
                }
            );
        }
    };

    // Check every 5 minutes
    setInterval(updateDaylightInfo, 1000 * 60 * 5);
})();
