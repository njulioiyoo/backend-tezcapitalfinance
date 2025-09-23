<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Complaint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class ComplaintController extends Controller
{
    public function store(Request $request)
    {
        try {
            // Rate limiting by IP address
            $key = 'complaint_submission:' . $request->ip();
            if (RateLimiter::tooManyAttempts($key, 3)) {
                $seconds = RateLimiter::availableIn($key);
                
                Log::warning('Complaint submission rate limit exceeded', [
                    'ip' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
                
                return response()->json([
                    'message' => 'Terlalu banyak percobaan. Silakan coba lagi dalam ' . ceil($seconds / 60) . ' menit.',
                    'errors' => [
                        'rate_limit' => ['Batas pengiriman terlampaui. Silakan tunggu beberapa saat.']
                    ]
                ], 429);
            }

            // Validate and sanitize input
            $validated = $request->validate([
                'name' => [
                    'required',
                    'string',
                    'min:2',
                    'max:255',
                    'regex:/^[a-zA-Z\s\-\.]+$/' // Only letters, spaces, hyphens, and dots
                ],
                'email' => [
                    'required',
                    'email:rfc,dns',
                    'max:255',
                    'not_regex:/[<>"\']/' // Prevent XSS characters
                ],
                'phone' => [
                    'nullable',
                    'string',
                    'max:20',
                    'regex:/^[\+]?[0-9\-\(\)\s]+$/' // Only numbers, +, -, (, ), and spaces
                ],
                'subject' => [
                    'required',
                    'string',
                    'min:5',
                    'max:255',
                    'not_regex:/[<>]/' // Prevent basic XSS
                ],
                'message' => [
                    'required',
                    'string',
                    'min:10',
                    'max:2000',
                    'not_regex:/[<>]/' // Prevent basic XSS
                ]
            ], [
                'name.required' => 'Nama lengkap wajib diisi.',
                'name.min' => 'Nama minimal 2 karakter.',
                'name.max' => 'Nama maksimal 255 karakter.',
                'name.regex' => 'Nama hanya boleh berisi huruf, spasi, tanda hubung, dan titik.',
                'email.required' => 'Email wajib diisi.',
                'email.email' => 'Format email tidak valid.',
                'email.not_regex' => 'Email mengandung karakter yang tidak diizinkan.',
                'phone.regex' => 'Format nomor telepon tidak valid.',
                'subject.required' => 'Subjek wajib diisi.',
                'subject.min' => 'Subjek minimal 5 karakter.',
                'subject.max' => 'Subjek maksimal 255 karakter.',
                'subject.not_regex' => 'Subjek mengandung karakter yang tidak diizinkan.',
                'message.required' => 'Pesan wajib diisi.',
                'message.min' => 'Pesan minimal 10 karakter.',
                'message.max' => 'Pesan maksimal 2000 karakter.',
                'message.not_regex' => 'Pesan mengandung karakter yang tidak diizinkan.',
            ]);

            // Additional security: Check for spam patterns
            $spamPatterns = [
                '/\b(viagra|cialis|lottery|casino|poker)\b/i',
                '/\b(free money|get rich|make money fast)\b/i',
                '/(http|https):\/\/[^\s]+/i', // URLs
                '/\b[A-Z]{10,}\b/', // Excessive caps
            ];

            foreach ($spamPatterns as $pattern) {
                if (preg_match($pattern, $validated['message'] . ' ' . $validated['subject'])) {
                    Log::warning('Spam pattern detected in complaint', [
                        'ip' => $request->ip(),
                        'pattern' => $pattern,
                        'subject' => $validated['subject'],
                    ]);
                    
                    return response()->json([
                        'message' => 'Konten tidak diizinkan. Pastikan pesan Anda sesuai dengan ketentuan.',
                        'errors' => [
                            'content' => ['Konten yang dikirim tidak sesuai dengan ketentuan platform.']
                        ]
                    ], 422);
                }
            }

            // Sanitize input data
            $validated['name'] = strip_tags(trim($validated['name']));
            $validated['email'] = filter_var(trim($validated['email']), FILTER_SANITIZE_EMAIL);
            $validated['phone'] = isset($validated['phone']) ? preg_replace('/[^0-9+\-().\s]/', '', trim($validated['phone'])) : null;
            $validated['subject'] = strip_tags(trim($validated['subject']));
            $validated['message'] = strip_tags(trim($validated['message']));

            // Add security metadata
            $validated['ip_address'] = $request->ip();
            $validated['user_agent'] = $request->userAgent();
            $validated['status'] = Complaint::STATUS_PENDING;

            // Create complaint
            $complaint = Complaint::create($validated);

            // Hit the rate limiter
            RateLimiter::hit($key);

            // Log successful submission
            Log::info('New complaint submitted', [
                'complaint_id' => $complaint->id,
                'ip' => $request->ip(),
                'email' => $validated['email'],
            ]);

            return response()->json([
                'message' => 'Pengaduan berhasil dikirim. Kami akan meninjau dan merespons dalam 1-3 hari kerja.',
                'data' => [
                    'id' => $complaint->id,
                    'reference_number' => 'TZC-' . str_pad($complaint->id, 6, '0', STR_PAD_LEFT),
                    'status' => $complaint->status_label,
                    'submitted_at' => $complaint->created_at->format('d/m/Y H:i'),
                ]
            ], 201);

        } catch (ValidationException $e) {
            // Log validation errors
            Log::warning('Complaint validation failed', [
                'ip' => $request->ip(),
                'errors' => $e->errors(),
                'input' => $request->except(['message']) // Don't log full message for privacy
            ]);

            return response()->json([
                'message' => 'Data yang dikirim tidak valid.',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Log unexpected errors
            Log::error('Complaint submission failed', [
                'ip' => $request->ip(),
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.',
                'errors' => [
                    'system' => ['Sistem sedang mengalami gangguan. Tim teknis telah diberitahu.']
                ]
            ], 500);
        }
    }
}