<?php

namespace App\Jobs;

use App\Models\Post;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessPostFile implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Post $post) {}

    public function handle(): void
    {
        // 1. Simular validación intensiva de antivirus en el PDF (Toma 5 segundos)
        sleep(2);

        // 2. Aquí iría lógica para extraer cantidad de páginas, texto, etc...
        // ...

        // 3. También podríamos enviar un email al maestro informando
        // de que se ha terminado de procesar con éxito el documento.

        // Por ejemplo, marcaríamos el archivo del post como procesado 
        // $this->post->update(['file_processed' => true]);
    }
}
