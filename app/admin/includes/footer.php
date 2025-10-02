        </div>
    </main>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-bs4.min.js"></script>
    
    <script>
        // Initialize Summernote for rich text editing
        $(document).ready(function() {
            $('.summernote').summernote({
                height: 300,
                toolbar: [
                    ['style', ['style']],
                    ['font', ['bold', 'underline', 'clear']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['table', ['table']],
                    ['insert', ['link', 'picture', 'video']],
                    ['view', ['fullscreen', 'codeview', 'help']]
                ]
            });
            
            // Auto-hide alerts after 5 seconds
            setTimeout(function() {
                $('.alert').fadeOut('slow');
            }, 5000);
            
            // Confirm delete actions
            $('.btn-danger[data-confirm]').click(function(e) {
                if (!confirm($(this).data('confirm'))) {
                    e.preventDefault();
                }
            });
            
            // File input preview
            $('input[type="file"][accept*="image"]').change(function() {
                const file = this.files[0];
                if (file) {
                    const reader = new FileReader();
                    const preview = $(this).siblings('.image-preview');
                    
                    reader.onload = function(e) {
                        if (preview.length) {
                            preview.attr('src', e.target.result).show();
                        } else {
                            $(this).after('<img class="image-preview img-thumbnail mt-2" src="' + e.target.result + '" style="max-width: 200px;">');
                        }
                    }.bind(this);
                    
                    reader.readAsDataURL(file);
                }
            });
        });
        
        // Mobile sidebar toggle
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('mobile-open');
        }
        
        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function(event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.querySelector('.mobile-toggle');
            
            if (window.innerWidth <= 768 && 
                sidebar && !sidebar.contains(event.target) && 
                toggle && !toggle.contains(event.target)) {
                sidebar.classList.remove('mobile-open');
            }
        });
        
        // Form validation
        (function() {
            'use strict';
            window.addEventListener('load', function() {
                var forms = document.getElementsByClassName('needs-validation');
                var validation = Array.prototype.filter.call(forms, function(form) {
                    form.addEventListener('submit', function(event) {
                        if (form.checkValidity() === false) {
                            event.preventDefault();
                            event.stopPropagation();
                        }
                        form.classList.add('was-validated');
                    }, false);
                });
            }, false);
        })();
    </script>
</body>
</html>