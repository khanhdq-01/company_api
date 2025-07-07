<h2>Thông tin liên hệ mới:</h2>
<p><strong>Tên:</strong> {{ $contact->name }}</p>
<p><strong>Email:</strong> {{ $contact->email }}</p>
<p><strong>Phone:</strong> {{ $contact->phone }}</p>
<p><strong>Tiêu đề:</strong> {{ $contact->subject }}</p>
<p><strong>Nội dung:</strong> {{ $contact->message }}</p>
<p><strong>CV:</strong>
    @if($contact->cv)
        <a href="{{ asset('storage/' . $contact->cv) }}" target="_blank">Xem CV</a>
    @else
        Không có
    @endif
</p>