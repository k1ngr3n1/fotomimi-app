# Upload Features Documentation

## Overview
The upload functionality has been enhanced to support larger batch uploads with improved progress tracking and user experience.

## New Features

### 1. Increased Upload Limit
- **Previous limit**: 20 files per upload
- **New limit**: 50 files per upload
- **File size limit**: 100MB per file
- **Total upload size**: Up to 5GB per batch (50 files × 100MB)

### 2. Progress Tracking
- **Real-time progress bar**: Shows upload percentage
- **File counter**: Displays "X of Y files uploaded"
- **Upload speed**: Shows files per second
- **Time estimation**: Calculates remaining time based on current speed
- **Total size display**: Shows combined size of all selected files

### 3. Enhanced Error Handling
- **Duplicate detection**: Prevents uploading the same file twice
- **File type validation**: Only allows supported image and video formats
- **Size validation**: Rejects files larger than 100MB
- **Detailed error messages**: Shows specific reasons for failed uploads
- **Partial success handling**: Continues upload even if some files fail

### 4. Improved User Interface
- **File limit indicator**: Shows "X of 50 files selected"
- **Total size display**: Shows combined file size
- **Upload status**: Visual indicators for upload progress
- **Responsive design**: Works on all screen sizes
- **Multilingual support**: Available in English and Croatian

## Supported File Types

### Images
- JPEG (.jpg, .jpeg)
- PNG (.png)
- GIF (.gif)
- WebP (.webp)
- BMP (.bmp)
- TIFF (.tiff)

### Videos
- MP4 (.mp4)
- AVI (.avi)
- MOV (.mov)
- WMV (.wmv)
- FLV (.flv)
- WebM (.webm)
- MKV (.mkv)

## Technical Implementation

### Backend Changes
- Updated `MediaController::store()` method to handle up to 50 files
- Added comprehensive error handling and logging
- Enhanced validation rules
- Improved file processing with try-catch blocks

### Frontend Changes
- Enhanced Vue.js component with progress tracking
- Added real-time upload statistics
- Implemented duplicate file detection
- Added file size validation
- Improved user feedback with toast notifications

### Server Configuration
- Updated `.htaccess` with PHP upload settings:
  - `upload_max_filesize`: 100M
  - `post_max_size`: 100M
  - `max_file_uploads`: 50
  - `max_execution_time`: 300 seconds
  - `memory_limit`: 256M

## Usage Instructions

1. **Select Files**: Drag and drop or click to browse files
2. **Review Selection**: Check file count and total size
3. **Choose Category**: Select appropriate category for upload
4. **Monitor Progress**: Watch real-time upload progress
5. **Handle Results**: Review success/error messages

## Performance Considerations

- **Memory usage**: Large uploads may require increased server memory
- **Network bandwidth**: Consider upload speed limitations
- **Storage space**: Ensure sufficient disk space for uploads
- **Processing time**: Large files may take longer to process

## Troubleshooting

### Common Issues
1. **Upload fails**: Check file size and type restrictions
2. **Slow uploads**: Monitor network connection and server performance
3. **Memory errors**: Increase PHP memory limit if needed
4. **Timeout errors**: Extend execution time for large uploads

### Error Messages
- "Maximum 50 files can be uploaded at once" - File count limit exceeded
- "File has unsupported extension" - Unsupported file type
- "File too large" - File exceeds 100MB limit
- "Duplicate file detected" - Same file already selected

## Future Enhancements

- **Chunked uploads**: For very large files
- **Resume capability**: Continue interrupted uploads
- **Batch processing**: Background processing for large uploads
- **Compression**: Automatic image/video compression
- **Thumbnail generation**: Automatic thumbnail creation
