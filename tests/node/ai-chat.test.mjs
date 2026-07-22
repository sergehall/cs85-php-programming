import assert from 'node:assert/strict';
import test from 'node:test';

import { characterCountLabel, parseSseEventBlock } from '../../resources/js/ai-chat.js';

test('parseSseEventBlock parses one JSON server-sent event', () => {
    const event = parseSseEventBlock('event: delta\ndata: {"type":"delta","content":"Hello"}');

    assert.deepEqual(event, {
        type: 'delta',
        content: 'Hello',
    });
});

test('parseSseEventBlock joins multiple data lines and ignores empty blocks', () => {
    assert.equal(parseSseEventBlock('event: ping'), null);
    assert.deepEqual(parseSseEventBlock('data: {"type":"status",\ndata: "content":"Working"}'), {
        type: 'status',
        content: 'Working',
    });
});

test('parseSseEventBlock rejects malformed application events', () => {
    assert.throws(() => parseSseEventBlock('data: {"content":"Missing type"}'), {
        name: 'TypeError',
    });
    assert.throws(() => parseSseEventBlock('data: not-json'), SyntaxError);
});

test('characterCountLabel formats composer usage consistently', () => {
    assert.equal(characterCountLabel(1250, 8000), '1,250 / 8,000');
});
