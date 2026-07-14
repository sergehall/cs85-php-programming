<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HobbyController extends Controller
{
    /**
     * Return the personalized sample data used by both hobby pages.
     *
     * In a later Laravel project, these records could come from a database.
     * For this routing assignment, a typed PHP array keeps the request flow
     * easy to inspect: route -> controller -> data -> Blade view.
     *
     * @return array<int, array{
     *     id: int,
     *     name: string,
     *     eyebrow: string,
     *     description: string,
     *     why_i_like_it: string,
     *     detail: string,
     *     icon: string
     * }>
     */
    private function getHobbies(): array
    {
        return [
            1 => [
                'id' => 1,
                'name' => 'Photography',
                'eyebrow' => 'Light, story, and timing',
                'description' => 'I enjoy creating portraits and visual stories through thoughtful lighting, composition, and editing.',
                'why_i_like_it' => 'Photography lets me combine technical problem-solving with creativity and preserve meaningful moments for other people.',
                'detail' => 'My photography work also inspires SERGIOARTG, a portfolio and booking platform where I can connect creative work with the web applications I build.',
                'icon' => 'camera',
            ],
            2 => [
                'id' => 2,
                'name' => 'Web Development',
                'eyebrow' => 'Ideas turned into interfaces',
                'description' => 'I like building accessible websites and learning how frontend and backend systems work together.',
                'why_i_like_it' => 'Web development gives me a practical way to solve problems, organize complex ideas, and continuously learn new tools.',
                'detail' => 'Laravel is especially interesting because routes, controllers, and Blade templates create a clear path from a browser request to a useful response.',
                'icon' => 'code',
            ],
            3 => [
                'id' => 3,
                'name' => 'Technology Projects',
                'eyebrow' => 'Learning by building',
                'description' => 'I enjoy experimenting with software tools, local development environments, and small projects that teach a new concept.',
                'why_i_like_it' => 'Hands-on projects help me understand how individual technologies connect and give me visible progress I can improve over time.',
                'detail' => 'A project may begin as a class exercise, but I like refining its structure, documentation, testing, and presentation until it becomes portfolio-quality work.',
                'icon' => 'spark',
            ],
        ];
    }

    /**
     * Display every hobby at GET /hobbies.
     */
    public function index(): View
    {
        return view('hobbies.index', [
            'embedded' => false,
            'hobbies' => $this->getHobbies(),
            'routePrefix' => '',
        ]);
    }

    /**
     * Display one hobby selected by the dynamic {id} route parameter.
     */
    public function show(int $id): View
    {
        $hobbies = $this->getHobbies();

        abort_unless(isset($hobbies[$id]), 404, 'Hobby not found');

        return view('hobbies.show', [
            'embedded' => false,
            'hobby' => $hobbies[$id],
            'routePrefix' => '',
        ]);
    }
}
