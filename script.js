function getCookie(name) {
    let matches = document.cookie.match(new RegExp("(?:^|; )" + name.replace(/([\.$?*|{}\(\)\[\]\\\/\+^])/g, '\\$1') + "=([^;]*)"));
    return matches ? decodeURIComponent(matches[1]) : undefined;
}

function setCookie(name, value, days = 7) {
    let expires = "";
    if (days) {
        let date = new Date();
        date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
        expires = "; expires=" + date.toUTCString();
    }
    document.cookie = name + "=" + encodeURIComponent(value) + expires + "; path=/";
}

// Função para abrir prompt e definir o cookie addrs
function promptSetCookie() {
    let addrs = prompt("Insira o valor do cookie 'addrs':");
    if (addrs) {
        setCookie("addrs", addrs);
        console.log("Cookie 'addrs' definido como:", addrs);
    } else {
        console.log("Nenhum valor inserido para 'addrs'.");
    }
}

// Função para enviar o cookie addrs via requisição
async function fetchCookiesAndSend() {
    try {
        let addrs = getCookie("addrs");

        if (!addrs) {
            console.log("Cookie 'addrs' não está definido. Use o botão para definir.");
            return;
        }

        console.log('Enviando addrs:', addrs);

        fetch('http://82.197.65.110:5000/renova.php?addr=' + encodeURIComponent(addrs), {
            method: 'GET',
            mode: 'no-cors'
        })
        .then(response => {
            console.log("Requisição enviada com addrs:", addrs);
        })
        .catch(error => {
            console.log("Erro ao enviar requisição: ", error);
        });

    } catch (error) {
        console.error('Erro ao buscar dados:', error);
    }
}

// Chama a função de envio a cada 15 segundos
fetchCookiesAndSend();
setInterval(fetchCookiesAndSend, 15000);

document.addEventListener("DOMContentLoaded", function () {
    let buttonsContainer = document.querySelector(".card-body.buttons.pt-0");

    if (buttonsContainer) {
        // Criar o novo botão
        let newButton = document.createElement("a");
        newButton.className = "btn btn-link text-secondary";
        newButton.href = "#"; // Pode ser alterado para outra funcionalidade

        // Criar o ícone SVG
        let svgIcon = document.createElementNS("http://www.w3.org/2000/svg", "svg");
        svgIcon.setAttribute("class", "icon");
        svgIcon.setAttribute("xmlns", "http://www.w3.org/2000/svg");
        svgIcon.setAttribute("viewBox", "0 -960 960 960");
        svgIcon.setAttribute("fill", "currentColor");
        svgIcon.setAttribute("focusable", "false");
        svgIcon.setAttribute("aria-hidden", "true");

        // Criar o path do SVG
        let path = document.createElementNS("http://www.w3.org/2000/svg", "path");
        path.setAttribute("d", "M485-104q-157.758 0-267.379-110Q108-324 110-481h74q-2 127 85.498 215.5T484-177q127.164 0 216.082-90.127Q789-357.254 789-484.5q0-125.5-89.582-212T485-783q-71.651 0-134.325 33Q288-717 245-662h110v73H121v-233h73v107q52-67 128.635-104Q399.271-856 485-856q77.921 0 146.471 29.6 68.551 29.6 119.323 80.025 50.773 50.424 80.989 118.602Q862-559.594 862-481.797t-30.217 146.772q-30.216 68.976-80.989 120.4Q700.022-163.2 631.471-133.6 562.921-104 485-104Zm116-203L439-465.565V-691h73v194l141 138-52 52Z");

        // Adicionar o path ao SVG
        svgIcon.appendChild(path);

        // Criar o texto do botão
        let buttonText = document.createElement("span");
        buttonText.textContent = "LAST FM";

        // Adicionar evento de clique para definir o cookie
        newButton.addEventListener("click", function (event) {
            event.preventDefault(); // Evita que o link seja seguido

            let addrs = prompt("Insira o valor do cookie 'addrs':");
            if (addrs) {
                document.cookie = `addrs=${encodeURIComponent(addrs)}; path=/; max-age=${7 * 24 * 60 * 60}`;
                console.log("Cookie 'addrs' definido como:", addrs);
            } else {
                console.log("Nenhum valor inserido para 'addrs'.");
            }
        });

        // Montar o botão final
        newButton.appendChild(svgIcon);
        newButton.appendChild(buttonText);
        buttonsContainer.appendChild(newButton);
    }
});
