@extends('admin.layout.admin-template')

@section('content')

    <div class="ml-4 text-sm">
        <h1>Stored File Detail</h1>


        <table>
            <tr>
                <td class="block w-32">Id</td>
                <td>
                    <form target="_blank" method="post" action="{{ route('adminStoredFileDownload') }}" enctype="multipart/form-data">
                        {{ csrf_field() }}

                        <input type="hidden" name="id" value="{{ $storedFileId }}" />
                        <button class="text-blue underline" type="submit">{{ $storedFileId }}</button>
                    </form>
                </td>
            </tr>
            @if($meta)
                <tr>
                    <td>Size</td>
                    <td>{{ round($meta->size / 1024, 2) }}kb</td>
                </tr>
                <tr>
                    <td>Mime</td>
                    <td>{{ $meta->mime }}</td>
                </tr>
                <tr>
                    <td>Encoding</td>
                    <td>{{ $meta->encoding }}</td>
                </tr>
                <tr>
                    <td>Identified as</td>
                    <td>{{ class_basename($meta->identified_as) }}</td>
                </tr>
                <tr>
                    <td>Line endings</td>
                    <td>{{ $meta->line_endings }}</td>
                </tr>
                <tr>
                    <td>Line count</td>
                    <td>{{ $meta->line_count }}</td>
                </tr>
            @else
                <tr>
                    <td colspan="2">Stored file meta not yet available</td>
                </tr>
            @endif
        </table>


<div class="flex bg-white m-4">

<pre class="pr-2 mr-2 border-r">
@foreach($lines as $line)
{{ $loop->iteration }}
@endforeach
</pre>

<pre class="overflow-y-scroll">
@foreach($lines as $line)
{{ $line }}
@endforeach
</pre>

</div>

    </div>

@endsection
