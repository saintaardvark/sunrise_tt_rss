(function () {
    let lastDate = new Date().toDateString();

    const updateDaylightInfo = () => {
        const currentDate = new Date().toDateString();

        if (currentDate !== lastDate) {
            console.log("Vancouver Sunrise: Date changed, updating toolbar...");

            xhr.post("backend.php", App.getPostReply({ op: "pluginhandler", plugin: "vancouver_sunrise", method: "update" }), (reply) => {
                const container = document.querySelector(".vancouver-sunrise-container");
                if (container && reply.responseText) {
                    // Update the inner content or replace the container
                    const temp = document.createElement("div");
                    temp.innerHTML = reply.responseText;
                    const newContainer = temp.firstChild;
                    if (newContainer) {
                        container.parentNode.replaceChild(newContainer, container);
                        lastDate = currentDate;
                    }
                }
            });
        }
    };

    // Check every 5 minutes
    setInterval(updateDaylightInfo, 1000 * 60 * 5);
})();
