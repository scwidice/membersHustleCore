// Add Activity Modal
document.addEventListener("DOMContentLoaded", function () {
    const addBtn = document.getElementById("addActivityBtn");
    const addModal = document.getElementById("addActivityModal");
    const addCloseBtn = document.getElementById("addCloseBtn");

    // Open modal
    addBtn.addEventListener("click", function () {
        addModal.style.display = "block";
    });

    // Close modal when clicking the X
    addCloseBtn.addEventListener("click", function () {
        addModal.style.display = "none";
    });

    // Close modal if clicking outside of modal content
    window.addEventListener("click", function (event) {
        if (event.target === addModal) {
            addModal.style.display = "none";
        }
    });


 // Edit Activity Modal
    const editModal = document.getElementById("editActivityModal");
    const editCloseBtn = document.getElementById("editCloseBtn");
    const editButtons = document.querySelectorAll(".editBtn");

    if (editModal && editCloseBtn && editButtons.length > 0) {
        editButtons.forEach(button => {
        button.addEventListener("click", function () {
            const activityId = button.getAttribute("data-activity-id");
            const duration = button.getAttribute("data-duration");
            const capacity = button.getAttribute("data-capacity");

            document.getElementById("editActivityId").value = activityId;
            document.getElementById("editDuration").value = duration;
            document.getElementById("editCapacity").value = capacity;

            editModal.style.display = "block";
            });
        });

        editCloseBtn.addEventListener("click", function () {
            editModal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === editModal) {
                editModal.style.display = "none";
            }
        });
    }

    //DELETE
    const deleteModal = document.getElementById("deleteActivityModal");
    const deleteCloseBtn = document.getElementById("deleteCloseBtn");
    const deleteButtons = document.querySelectorAll(".deleteBtn");

    if (deleteModal && deleteCloseBtn && deleteButtons.length > 0) {
        deleteButtons.forEach(button => {
            button.addEventListener("click", function () {
                const activityId = button.getAttribute("data-activity-id");
                document.getElementById("deleteActivityId").value = activityId;
                deleteModal.style.display = "block";
            });
        });

        deleteCloseBtn.addEventListener("click", () => {
            deleteModal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === deleteModal) {
                deleteModal.style.display = "none";
            }
        });
    }

});

document.addEventListener("DOMContentLoaded", function() {
    const addMemberBtn = document.getElementById("addMemberBtn");
    const addMemberModal = document.getElementById("addMemberModal");
    const addMemberCloseBtn = document.getElementById("addMemberCloseBtn");

    // Open  modal
    addMemberBtn.addEventListener("click", () => {
        addMemberModal.style.display = "block";
    });

    // close button
    addMemberCloseBtn.addEventListener("click", () => {
        addMemberModal.style.display = "none";
    });

    // Close modal when clicking outside of it
    window.addEventListener("click", (event) => {
        if (event.target === addMemberModal) {
            addMemberModal.style.display = "none";
        }
    });

    // Edit Member Modal
    const editMemberModal = document.getElementById("editMemberModal");
    const editMemberCloseBtn = document.getElementById("editMemberCloseBtn");
    const editButtons = document.querySelectorAll(".editBtn");

    if (editMemberModal && editMemberCloseBtn && editButtons.length > 0) {
        editButtons.forEach(button => {
            button.addEventListener("click", function () {
                const memberId = button.getAttribute("data-member-id");
                const firstName = button.getAttribute("data-first-name");
                const lastName = button.getAttribute("data-last-name");
                const email = button.getAttribute("data-email");

                document.getElementById("editMemberID").value = memberId;
                document.getElementById("editFirstName").value = firstName;
                document.getElementById("editLastName").value = lastName;
                document.getElementById("editEmail").value = email;

                editMemberModal.style.display = "block";
            });
        });

        editMemberCloseBtn.addEventListener("click", function () {
            editMemberModal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === editMemberModal) {
                editMemberModal.style.display = "none";
            }
        });
    }

    //DELETE member
    const deleteMemberModal = document.getElementById("deleteMemberModal");
    const deleteMemberCloseBtn = document.getElementById("deleteMemberCloseBtn");
    const deleteMemberButtons = document.querySelectorAll(".deleteBtn");
    
    if (deleteMemberModal && deleteMemberCloseBtn && deleteMemberButtons.length > 0) {
        deleteMemberButtons.forEach(button => {
            button.addEventListener("click", function () {
                const memberId = button.getAttribute("data-member-id");
                document.getElementById("deleteMemberId").value = memberId;
                deleteMemberModal.style.display = "block";
            });
        });

        deleteMemberCloseBtn.addEventListener("click", () => {
            deleteMemberModal.style.display = "none";
        });

        window.addEventListener("click", function (event) {
            if (event.target === deleteMemberModal) {
                deleteMemberModal.style.display = "none";
            }
        });
    }

});
