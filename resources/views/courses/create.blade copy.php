<!DOCTYPE html>
<html>

<head>
    <title>Create Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
</head>

<body>
    <div class="container mt-4">
        <h2>Create Course</h2>
        <form id="course-form">
            @csrf
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" required></textarea>
            </div>

            <button type="button" id="add-module" class="btn btn-secondary mb-3">Add Module +</button>

            <div class="accordion" id="modules-wrapper"></div>

            <button type="submit" class="btn btn-success mt-2">Save</button>
            <button type="button" class="btn btn-danger mt-2">Cancel</button>
        </form>
    </div>

    <script>
        let moduleCount = 0;

        $('#add-module').click(function () {
            moduleCount++;
            const moduleId = `module-${moduleCount}`;
            const contentId = `content-${moduleCount}`;
            const moduleHTML = `
                <div class="card module" data-index="${moduleCount}">
                    <div class="card-header" id="heading-${moduleId}">
                        <h2 class="mb-0 d-flex justify-content-between align-items-center">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-${moduleId}" aria-expanded="true" aria-controls="collapse-${moduleId}">
                                Module ${moduleCount}
                            </button>
                        </h2>
                        <button type="button" class="btn btn-sm btn-danger delete-module">X</button>
                    </div>

                    <div id="collapse-${moduleId}" class="collapse show" data-parent="#modules-wrapper">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Module Title</label>
                                <input type="text" name="modules[${moduleCount}][title]" class="form-control" required>
                            </div>

                            <button type="button" class="btn btn-sm btn-info add-content mb-2">Add Content +</button>

                            <div class="accordion" id="${contentId}"></div>
                        </div>
                    </div>
                </div>
            `;
            $('#modules-wrapper').append(moduleHTML);
        });

        $(document).on('click', '.add-content', function () {
            const moduleCard = $(this).closest('.module');
            const moduleIndex = moduleCard.data('index');
            const contentWrapper = moduleCard.find('.accordion');
            const contentCount = contentWrapper.children().length + 1;
            const contentId = `content-${moduleIndex}-${contentCount}`;

            const contentHTML = `
                <div class="card mb-2">
                    <div class="card-header" id="heading-${contentId}">
                        <h2 class="mb-0 d-flex justify-content-between align-items-center">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapse-${contentId}" aria-expanded="true" aria-controls="collapse-${contentId}">
                                Content ${contentCount}
                            </button>
                            <button type="button" class="btn btn-sm btn-danger delete-content">X</button>
                        </h2>
                    </div>
                    <div id="collapse-${contentId}" class="collapse show">
                        <div class="card-body">
                            <div class="form-group">
                                <label>Content Title</label>
                                <input type="text" name="modules[${moduleIndex}][contents][][data]" class="form-control" placeholder="Enter content" required>
                            </div>
                            <div class="form-group">
                                <label>Content Type</label>
                                <select name="modules[${moduleIndex}][contents][][type]" class="form-control">
                                    <option value="text">Text</option>
                                    <option value="video">Video</option>
                                    <option value="image">Image</option>
                                    <option value="link">Link</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            `;
            contentWrapper.append(contentHTML);
        });

        // Delete module
        $(document).on('click', '.delete-module', function () {
            $(this).closest('.module').remove();
        });

        // Delete content
        $(document).on('click', '.delete-content', function () {
            $(this).closest('.card').remove();
        });

        // Form submission
        $('#course-form').on('submit', function (e) {
            e.preventDefault();
            $.ajax({
                url: '/courses',
                type: 'POST',
                data: $(this).serialize(),
                success: function (res) {
                    alert(res.message);
                    location.reload();
                },
                error: function (err) {
                    alert("Error: " + err.responseJSON.message);
                }
            });
        });
    </script>

    <!-- Required Bootstrap JS for accordions -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
