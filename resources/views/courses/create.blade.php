<!DOCTYPE html>
<html>

<head>
    <title>Create Course</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        .delete-btn {
            float: right;
            margin-left: 10px;
        }

        .card-header {
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .toggle-icon {
            margin-left: auto;
        }
    </style>
</head>

<body>
    <div class="container mt-4">
        <h2>Create Course</h2>
        <form id="course-form">
            @csrf
            <div class="form-group">
                <label>Title</label>
                <input type="text" name="title" class="form-control">
            </div>
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control"></textarea>
            </div>

            <button type="button" id="add-module" class="btn btn-primary mb-3">Add Module +</button>
            <div id="modules-wrapper" class="accordion"></div>

            <button type="submit" class="btn btn-success">Save</button>
            <button type="button" class="btn btn-danger">Cancel</button>
        </form>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>


    <script>
        let moduleCount = 0;

        $('#add-module').click(function() {
            moduleCount++;
            const moduleId = `module-${moduleCount}`;
            const contentId = `content-${moduleCount}`;
            const moduleHTML = `
            <div class="card module mb-3" data-index="${moduleCount}" data-content-count="0">
                <div class="card-header" data-toggle="collapse" data-target="#collapse-${moduleId}" aria-expanded="true">
                    <span>Module ${moduleCount}</span>
                    <div>
                        <span class="toggle-icon">&#9660;</span>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-module delete-btn">&times;</button>
                    </div>
                </div>
                <div id="collapse-${moduleId}" class="collapse show">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Module Title</label>
                            <input type="text" name="modules[${moduleCount}][title]" class="form-control">
                        </div>
                        <button type="button" class="btn btn-sm btn-primary add-content mb-2">Add Content +</button>
                        <div class="accordion contents"></div>
                    </div>
                </div>
            </div>`;
            $('#modules-wrapper').append(moduleHTML);
        });

        $(document).on('click', '.add-content', function() {
            const moduleCard = $(this).closest('.module');
            const moduleIndex = moduleCard.data('index');
            let contentCount = moduleCard.data('content-count') || 0;
            contentCount++;
            moduleCard.data('content-count', contentCount);

            const contentId = `content-${moduleIndex}-${contentCount}`;
            const contentHTML = `
            <div class="card mb-2">
                <div class="card-header" data-toggle="collapse" data-target="#collapse-${contentId}" aria-expanded="true">
                    <span>Content ${contentCount}</span>
                    <div>
                        <span class="toggle-icon">&#9660;</span>
                        <button type="button" class="btn btn-sm btn-outline-danger delete-content delete-btn">&times;</button>
                    </div>
                </div>
                <div id="collapse-${contentId}" class="collapse show">
                    <div class="card-body">
                        <div class="form-group">
                            <label>Content Title</label>
                            <input type="text" name="modules[${moduleIndex}][contents][${contentCount}][data]" class="form-control" placeholder="Enter content">
                        </div>
                        <div class="form-group">
                            <label>Content Type</label>
                            <select name="modules[${moduleIndex}][contents][${contentCount}][type]" class="form-control">
                                <option value="text">Text</option>
                                <option value="video">Video</option>
                                <option value="image">Image</option>
                                <option value="link">Link</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>`;
            moduleCard.find('.contents').append(contentHTML);
        });

        $(document).on('click', '.delete-module', function() {
            $(this).closest('.module').remove();
        });

        $(document).on('click', '.delete-content', function() {
            $(this).closest('.card').remove();
        });

        $('#course-form').on('submit', function(e) {
            e.preventDefault();
            $('.error').remove(); 

            $.ajax({
                url: '/courses',
                type: 'POST',
                data: $(this).serialize(),
                headers: {
                    'Accept': 'application/json' 
                },
                success: function(res) {
                    alert(res.message);
                    location.reload();
                },
                error: function(err) {
                    if (err.status === 422) {
                        const errors = err.responseJSON.errors;

                        $.each(errors, function(field, messages) {
                            const nameAttr = field.replace(/\.(\d+)/g, '[$1]').replace(/\.(\w+)/g, '[$1]');
                            const input = $(`[name="${nameAttr}"]`);

                            if (input.length) {
                                const errorDiv = $('<div class="text-danger error mt-1"></div>').text(messages[0]);
                                input.after(errorDiv);
                            }
                        });
                    } else {
                        alert("An unknown error occurred.");
                    }
                }
            });
        });
    </script>

</body>

</html>