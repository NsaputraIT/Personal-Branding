/**
 * CDN Uploader — client-side image processing + presigned URL upload.
 *
 * Flow:
 *   1. process(file)   → Canvas resize (max 1920px) → WebP encode (quality 0.8)
 *   2. upload(blob, presignedUrl) → PUT directly to CDN via fetch
 */
const cdnUploader = {
    /**
     * Process an image file client-side: resize + WebP encode.
     *
     * @param {File} file  The raw file from an <input type="file">
     * @param {object} [opts]
     * @param {number} [opts.maxDimension=1920]  Longest side in pixels
     * @param {number} [opts.quality=0.8]        WebP quality 0–1
     * @returns {Promise<Blob>}  The processed WebP blob
     */
    async process(file, { maxDimension = 1920, quality = 0.8 } = {}) {
        const bitmap = await createImageBitmap(file);

        // Calculate new dimensions maintaining aspect ratio
        let { width, height } = bitmap;
        if (width > height && width > maxDimension) {
            height = Math.round(height * (maxDimension / width));
            width = maxDimension;
        } else if (height > maxDimension) {
            width = Math.round(width * (maxDimension / height));
            height = maxDimension;
        }

        const canvas = document.createElement('canvas');
        canvas.width = width;
        canvas.height = height;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(bitmap, 0, 0, width, height);
        bitmap.close();

        return new Promise((resolve, reject) => {
            canvas.toBlob(
                (blob) => {
                    if (blob) resolve(blob);
                    else reject(new Error('Canvas toBlob returned null'));
                },
                'image/webp',
                quality,
            );
        });
    },

    /**
     * Upload a processed blob directly to CDN via a presigned PUT URL.
     *
     * @param {Blob} blob            The processed WebP blob
     * @param {string} presignedUrl  S3 presigned PUT URL
     * @returns {Promise<boolean>}   True on success
     */
    async upload(blob, presignedUrl) {
        const response = await fetch(presignedUrl, {
            method: 'PUT',
            body: blob,
            headers: {
                'Content-Type': 'image/webp',
            },
        });

        if (!response.ok) {
            throw new Error(`Upload failed: ${response.status} ${response.statusText}`);
        }

        return true;
    },

    /**
     * Full upload pipeline: process → presign → upload.
     *
     * @param {File} file              Raw file from input
     * @param {string} directory       Bucket subdirectory (e.g. 'hero')
     * @param {Function} getPresigned  Async fn(directory) => { url, path }
     * @param {Function} setPath       Callback(path) to store the CDN path
     * @param {object} [opts]          Optional process/upload overrides
     */
    async fullPipeline(file, directory, getPresigned, setPath, opts = {}) {
        // 1. Process client-side
        const blob = await this.process(file, opts);

        // 2. Get presigned URL from server
        const { url, path } = await getPresigned(directory);

        // 3. Upload to CDN
        await this.upload(blob, url);

        // 4. Store resulting path
        setPath(path);

        return path;
    },
};

// Expose globally for Livewire/Alpine usage
window.cdnUploader = cdnUploader;
