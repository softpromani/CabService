
$(document).ready(() => {
    let selectedImages = [];

    $("#select-image").on('change', function() {
        let files = this.files;
        for (let index = 0; index < files.length; index++) {
            selectedImages.push(files[index]);
        }
        displaySelectedImages();
        this.value = null;
    });

    function displaySelectedImages() {
        const containerImage = $("#selected-image-container");
        const imageArray = $(".image-array");

        containerImage.empty();
        imageArray.empty();

        selectedImages.forEach((file, index) => {
            let fileReader = new FileReader();
            fileReader.onload = function(event) {
                let imageSrc = event.target.result;

                let imageDiv = $(`
            <div class="upload_img_box position-relative">
                <span class="img-clear position-absolute top-0 end-0 p-1 bg-danger text-white imgcross" data-index="${index}">×</span>
                <img src="${imageSrc}" class="rounded border" width="80" height="80">
            </div>
        `);
                imageArray.append(imageDiv);
            };
            fileReader.readAsDataURL(file);
        });

        $(".img-clear").on("click", function() {
            let removeIndex = $(this).data("index");
            selectedImages.splice(removeIndex, 1);
            displaySelectedImages();
        });
    }

    $(".needs-validation").on("submit", function(e) {
        e.preventDefault();
        let formData = new FormData(this);

        // Add selected images to FormData
        selectedImages.forEach((file, index) => {
            formData.append("attachment[]", file);
        });

        $.ajax({
            url: $(this).attr("action"),
            method: $(this).attr("method"),
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                toastr.success("Reply sent successfully!");
                window.location.reload();
            },
            error: function(xhr) {
                toastr.error("Something went wrong!");
            }
        });
    });
});