<?php
require_once __DIR__ . '/cookie.php';

$userId = checkAuthCookie();
if ($userId === null) {
    header('Location: /login.html');
    exit;
}
?>

<!DOCTYPE html>
<html lang="en" class="bg-gradient-to-br from-blue-200 via-white to-purple-200 bg-fixed">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Contact Manager</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="min-h-screen max-h-screen flex flex-col">


    <nav class="bg-white shadow px-4 py-3 flex justify-center">
        <div class="flex justify-between items-center max-w-6xl w-full">
            <a href="" class="text-blue-600 font-semibold text-[26px]">MyContacts</a>
            <button id="logoutButton" onclick="logout()" class="px-4 py-2 bg-white text-blue-600 border border-blue-600 rounded hover:bg-blue-100">Logout</button>

        </div>
    </nav>

    <section class="bg-transparent px-4 py-3 flex flex-1 justify-center min-h-0">
        <div class="flex max-w-6xl w-full flex-1 bg-gray-200/30 rounded-md p-2 gap-2 min-h-0">
            <div id="leftBar" class="flex flex-col max-w-xs w-full flex-1 bg-white rounded-md shadow-md min-h-0">
                <!-- Search bar + button -->
                <div class="flex items-center p-2 gap-2">
                    <div class="relative flex-1">
                    <input 
                        id="searchInput"
                        type="text" 
                        placeholder="Search..." 
                        class="w-full border rounded-md pl-2 pr-8 py-1 text-sm"
                    />
                    <button 
                        id="searchBtn"
                        type="button"
                        class="absolute right-2 top-1/2 -translate-y-1/2"
                        aria-label="Search"
                    >
                        <img src="./media/searchicon.png" class="w-4 h-4 opacity-70" alt="" />
                    </button>
                    </div>
                    <button class="h-full flex-1 max-w-[30px] bg-blue-500 text-white rounded-md text-md" onclick="createContactPage()">✚</button>
                </div>

                <!-- Scrolling frame -->
                <div id="results" class="overflow-y-auto p-2 text-sm text-gray-800 space-y-2 min-h-0">

                </div>
            </div>

            <div id="rightBar" class="flex flex-col max-w-6xl w-full flex-1">
                <button id="rightBarBtn" onclick="closeRightBar()" class="ml-auto w-7 h-7 bg-blue-500 text-white rounded-md font-bold flex items-center justify-center mb-2 hidden">⮌</button>

                <div id="viewContact" class="flex w-full flex-1 flex-col bg-white rounded-md shadow-md p-6 hidden">
                    <div class="flex">
                        <h1 id="VCTitle" class="text-neutral-700 font-semibold text-[30px] mr-auto">Contact Name</h1>

                        <button type="button" 
                                onclick="editContactPage()" 
                                class="inline-flex items-center justify-center w-10 h-10 rounded hover:bg-blue-100 opacity-70">
                        <img src="../media/editicon.png" alt="Edit" class="w-5 h-5">
                        </button>

                        <button type="button" 
                                onclick="deleteContact()" 
                                class="inline-flex items-center justify-center w-10 h-10 rounded hover:bg-red-100 opacity-70">
                        <img src="../media/deleteicon.png" alt="Delete" class="w-5 h-5">
                        </button>

                    </div>
                    <div class="w-full bg-gray-200 h-[2px] mt-[5px] mb-[15px]"></div>

                    <h2 class="text-neutral-500 text-[15px]">First Name</h1>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <h2 id="VCFirstName" class="text-neutral-700 text-[18px] mb-[14px]">Skyler</h1>

                    <h2 class="text-neutral-500 text-[15px]">Last Name</h1>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <h2 id="VCLastName" class="text-neutral-700 text-[18px] mb-[14px]">Quinby</h1>

                    <h2 class="text-neutral-500 text-[15px]">Phone Number</h1>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <h2 id="VCPhone" class="text-neutral-700 text-[18px] mb-[14px]">123-456-7890</h1>

                    <h2 class="text-neutral-500 text-[15px]">Email</h1>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <h2 id="VCEmail" class="text-neutral-700 text-[18px] mb-[14px]">skyler@examle.com</h1>

                    <h2 class="text-neutral-500 text-[15px]">Notes</h1>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <h2 id="VCNotes" class="text-neutral-700 text-[18px] mb-[14px]">These are some notes about this contact</h1>
                </div>

                <form id="createContact" class="flex w-full flex-1 flex-col bg-white rounded-md shadow-md p-6 hidden">
                    <div class="flex">
                        <h1 class="text-neutral-700 font-semibold text-[30px] mr-auto">Create Contact</h1>
                    </div>

                    <div class="w-full bg-gray-200 h-[2px] mt-[5px] mb-[15px]"></div>

                    <h2 class="text-neutral-500 text-[15px]">First Name</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="CCFirstName" name="first_name"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Last Name</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="CCLastName" name="last_name"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Phone Number</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="CCPhone" name="phone"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Email</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="CCEmail" name="email"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Notes</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <textarea id="CCNotes" name="notes" rows="3"
                                class="w-full max-w-[25rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]"></textarea>

                    <button type="submit"
                            class="mt-4 w-full max-w-[10rem] bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
                        Create Contact
                    </button>

                    <p id="errorMsg" class="mt-2 text-red-500 text-md mt-4 font-semibold"></p>
                </form>

                <form id="editContact" class="flex w-full flex-1 flex-col bg-white rounded-md shadow-md p-6">
                    <div class="flex">
                        <h1 id="ECTitle" class="text-neutral-700 font-semibold text-[30px] mr-auto">Edit Contact</h1>
                    </div>

                    <div class="w-full bg-gray-200 h-[2px] mt-[5px] mb-[15px]"></div>

                    <h2 class="text-neutral-500 text-[15px]">First Name</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="ECFirstName" name="first_name"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Last Name</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="ECLastName" name="last_name"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Phone Number</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="ECPhone" name="phone"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Email</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <input type="text" id="ECEmail" name="email"
                            class="w-full max-w-[13rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]" />

                    <h2 class="text-neutral-500 text-[15px]">Notes</h2>
                    <div class="w-[65%] bg-gray-200 h-[2px] mt-[0px] mb-[5px]"></div>
                    <textarea id="ECNotes" name="notes" rows="3"
                                class="w-full max-w-[25rem] border border-gray-300 rounded px-2 py-1 text-neutral-700 text-[18px] mb-[14px]"></textarea>

                    <button type="submit"
                            class="mt-4 w-full max-w-[10rem] bg-blue-600 text-white rounded px-4 py-2 hover:bg-blue-700">
                        Update Contact
                    </button>

                    <p id="updErrorMsg" class="mt-2 text-red-500 text-md mt-4 font-semibold"></p>
                </form>

                
            </div>
        </div>
    </section>


</body>

<script>

let rows = [];
let wasMobile = false;

const leftBar = document.getElementById('leftBar');
const rightBar = document.getElementById('rightBar');
const rightBarBtn = document.getElementById('rightBarBtn');

let setting = -1;


const viewContactElement = document.getElementById('viewContact');
const createContactElement = document.getElementById("createContact");
const editContactElement = document.getElementById("editContact");

async function logout() {
  try {
    const response = await fetch("/api/auth/logout", { method: "POST" });
    if (response.ok) {
      window.location.href = "/login.html";
    } else {
      console.error("Logout failed");
    }
  } catch (err) {
    console.error("Error:", err);
  }
}



function viewContactPage(i) {
    setting = i;
    createContactElement.classList.add("hidden");
    editContactElement.classList.add("hidden");
    viewContactElement.classList.remove("hidden");
    document.getElementById("errorMsg").textContent = '';
    document.getElementById("updErrorMsg").textContent = '';

    document.getElementById("VCTitle").textContent = rows[i].first_name + ' ' + rows[i].last_name;
    document.getElementById("VCFirstName").textContent = rows[i].first_name;
    document.getElementById("VCLastName").textContent = rows[i].last_name;
    document.getElementById("VCPhone").textContent = rows[i].phone_number;
    document.getElementById("VCEmail").textContent = rows[i].email;
    document.getElementById("VCNotes").textContent = rows[i].notes;

    if(wasMobile) {
        leftBar.classList.add("hidden");
        rightBar.classList.remove("hidden");
    }

}

function createContactPage() {
    viewContactElement.classList.add("hidden");
    editContactElement.classList.add("hidden");
    createContactElement.classList.remove("hidden");

    document.getElementById("errorMsg").textContent = '';
    document.getElementById("CCFirstName").value = "";
    document.getElementById("CCLastName").value = "";
    document.getElementById("CCPhone").value = "";
    document.getElementById("CCEmail").value = "";
    document.getElementById("CCNotes").value = "";

    if(wasMobile) {
        leftBar.classList.add("hidden");
        rightBar.classList.remove("hidden");
    }
}

function editContactPage() {
    viewContactElement.classList.add("hidden");
    editContactElement.classList.remove("hidden");
    createContactElement.classList.add("hidden");

    document.getElementById("ECTitle").textContent = rows[setting].first_name + ' ' + rows[setting].last_name;
    document.getElementById("ECFirstName").value = rows[setting].first_name;
    document.getElementById("ECLastName").value = rows[setting].last_name;
    document.getElementById("ECPhone").value = rows[setting].phone_number;
    document.getElementById("ECEmail").value = rows[setting].email;
    document.getElementById("ECNotes").value = rows[setting].notes;
}

async function deleteContact() {
  const contact_id = rows[setting].contact_id;

  try {
    const response = await fetch("/api/contacts.php", {
      method: "DELETE",
      headers: { "Content-Type": "application/json" },
      body: JSON.stringify({ contact_id })
    });

    const result = await response.json();
    createContactPage();
    runSearch();
  } catch (err) {
    console.error("Error:", err);
  }
}


createContactElement.addEventListener("submit", async function (e) {
	e.preventDefault();

	const errorMsg = document.getElementById("errorMsg");
	errorMsg.textContent = "";

	const first_name = document.getElementById("CCFirstName").value.trim();
	const last_name = document.getElementById("CCLastName").value.trim();
	const phone_number = document.getElementById("CCPhone").value.trim();
	const email = document.getElementById("CCEmail").value.trim();
	const notes = document.getElementById("CCNotes").value.trim();

	if (!first_name || !last_name) {
		errorMsg.textContent = "First name and last name are required";
		return;
	}

    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errorMsg.textContent = "Please enter a valid email";
		return;
    }

    if (phone_number && !/^\+?[0-9\s\-()]{7,20}$/.test(phone_number)) {
    errorMsg.textContent = "Please enter a valid phone number";
    return;
}


	try {
		const response = await fetch("/api/contacts.php", {
			method: "POST",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ first_name, last_name, phone_number, email, notes })
		});
		const result = await response.json();

		if (response.ok) {
			this.reset();
            document.getElementById('searchInput').value = '';
            await runSearch();
            viewContactPage(0);

			errorMsg.textContent = result.message;
		} else {
			errorMsg.textContent = result.message || "Failed to create contact";
		}
	} catch (err) {
		console.error("Error:", err);
		errorMsg.textContent = "An error occurred. Please try again.";
	}
});

editContactElement.addEventListener("submit", async function (e) {
	e.preventDefault();

	const errorMsg = document.getElementById("updErrorMsg");
	errorMsg.textContent = "";

	const first_name = document.getElementById("ECFirstName").value.trim();
	const last_name = document.getElementById("ECLastName").value.trim();
	const phone_number = document.getElementById("ECPhone").value.trim();
	const email = document.getElementById("ECEmail").value.trim();
	const notes = document.getElementById("ECNotes").value.trim();
    const contact_id = rows[setting].contact_id;

	if (!first_name || !last_name) {
		errorMsg.textContent = "First name and last name are required";
		return;
	}

    if (email && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        errorMsg.textContent = "Please enter a valid email";
		return;
    }

        if (phone_number && !/^\+?[0-9\s\-()]{7,20}$/.test(phone_number)) {
    errorMsg.textContent = "Please enter a valid phone number";
    return;
}

	try {
		const response = await fetch("/api/contacts.php", {
			method: "PUT",
			headers: { "Content-Type": "application/json" },
			body: JSON.stringify({ first_name, last_name, phone_number, email, notes, contact_id })
		});
		const result = await response.json();

		if (response.ok) {
			this.reset();
            rows[setting].first_name = first_name;
            rows[setting].last_name = last_name;
            rows[setting].phone_number = phone_number;
            rows[setting].email = email;
            rows[setting].notes = notes;
            errorMsg.textContent = result.message;
            viewContactPage(setting);
            runSearch();
		} else {
            if(result.message === 'No changes made') {
                viewContactPage(setting);
                runSearch();
            } else {
                errorMsg.textContent = result.message || "Failed to update contact";
            }
		}
	} catch (err) {
		console.error("Error:", err);
		errorMsg.textContent = "An error occurred. Please try again.";
	}
});


  const input = document.getElementById('searchInput');
  const btn   = document.getElementById('searchBtn');
  const out   = document.getElementById('results');

  async function runSearch() {
    const q = input.value.trim();
    try {
      const res = await fetch(`/api/contacts?search=${encodeURIComponent(q)}`);
      if (!res.ok) throw new Error(`HTTP ${res.status}`);
      const data = await res.json();

      rows = Array.isArray(data.rows) ? data.rows : [];

if (!data.success || rows.length === 0) {
  out.innerHTML = `
    <div class="text-gray-500 text-lg text-center font-medium">
      Create your first contact!
    </div>`;
  return;
}


        out.innerHTML = rows.map((r, i) => `
        <button 
        onclick="viewContactPage(${i})" 
        class="block text-left w-full border rounded-md p-2 hover:bg-gray-100"
        >
        <div class="font-semibold">${r.first_name ?? ''} ${r.last_name ?? ''}</div>
        <div class="text-sm text-gray-600">${r.phone_number ?? ''}</div>
        <div class="text-sm text-gray-600">${r.email ?? ''}</div>
        </button>


        `).join('');

    } catch (e) {
      out.innerHTML = `<div class="text-red-600">Error: ${e.message}</div>`;
    }
  }

  btn.addEventListener('click', runSearch);
  input.addEventListener('keydown', (e) => { if (e.key === 'Enter') runSearch(); });

  runSearch();
createContactPage();

//mobile support

const checkDevice = () => {
  const isMobile = window.innerWidth < 840;
  if (isMobile !== wasMobile) {
    wasMobile = isMobile;
    if (isMobile) {
      console.log("Now mobile");
      leftBar.classList.add("hidden");
      rightBarBtn.classList.remove("hidden");
      rightBar.classList.remove("hidden");
      leftBar.classList.remove("max-w-xs");
      // mobile behavior
    } else {
      console.log("Now desktop");
      rightBarBtn.classList.add("hidden");
      leftBar.classList.remove("hidden");
        rightBar.classList.remove("hidden");
        leftBar.classList.add("max-w-xs");
    }
  }
}

window.addEventListener("resize", checkDevice);

function closeRightBar() {
    rightBar.classList.add("hidden");
    leftBar.classList.remove("hidden");
}

checkDevice();



</script>

</html>
