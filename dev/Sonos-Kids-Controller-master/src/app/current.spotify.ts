export interface CurrentSpotify {
    progress_ms?: number;
    item?: {
        album?: {
            name?: string;
            total_tracks?: number;
        };
        duration_ms?: number;
        id?: string;
        name?: string;
        track_number?: number;
    };
}